<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Core\Domain\AmazonAddressFactory;
use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Payment\Api\AddressManagementInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Helper\Address;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Quote\Model\Quote;
use PayWithAmazon\ResponseInterface;

class AddressManagement implements AddressManagementInterface
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var Address
     */
    protected $addressHelper;

    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CollectionFactory
     */
    protected $countryCollectionFactory;

    /**
     * @var AmazonAddressFactory
     */
    private $amazonAddressFactory;

    /**
     * @param ClientFactoryInterface $clientFactory
     * @param Address $addressHelper
     * @param QuoteLinkInterfaceFactory $quoteLinkFactory
     * @param Session $session
     * @param CollectionFactory $countryCollectionFactory
     * @param AmazonAddressFactory $amazonAddressFactory
     */
    public function __construct(
        ClientFactoryInterface $clientFactory,
        Address $addressHelper,
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        Session $session,
        CollectionFactory $countryCollectionFactory,
        AmazonAddressFactory $amazonAddressFactory
    ) {
        $this->clientFactory            = $clientFactory;
        $this->addressHelper            = $addressHelper;
        $this->quoteLinkFactory         = $quoteLinkFactory;
        $this->session                  = $session;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->amazonAddressFactory     = $amazonAddressFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress($amazonOrderReferenceId, $addressConsentToken)
    {
        try {
            $data = $this->getOrderReferenceDetails($amazonOrderReferenceId, $addressConsentToken);

            $this->updateQuoteLink($amazonOrderReferenceId);

            if (isset($data['OrderReferenceDetails']['Destination']['PhysicalDestination'])) {
                $shippingAddress = $data['OrderReferenceDetails']['Destination']['PhysicalDestination'];

                return $this->convertToMagentoAddress($shippingAddress, true);
            }

            throw new Exception();
        } catch (WebapiException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->throwUnknownErrorException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress($amazonOrderReferenceId, $addressConsentToken)
    {
        try {
            $data = $this->getOrderReferenceDetails($amazonOrderReferenceId, $addressConsentToken);

            $this->updateQuoteLink($amazonOrderReferenceId);

            if (isset($data['OrderReferenceDetails']['BillingAddress']['PhysicalAddress'])) {
                $billingAddress = $data['OrderReferenceDetails']['BillingAddress']['PhysicalAddress'];

                return $this->convertToMagentoAddress($billingAddress);
            } elseif (isset($data['OrderReferenceDetails']['Destination']['PhysicalDestination'])) {
                $billingAddress = $data['OrderReferenceDetails']['Destination']['PhysicalDestination'];

                return $this->convertToMagentoAddress($billingAddress);
            }

            throw new Exception();
        } catch (WebapiException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->throwUnknownErrorException();
        }
    }

    protected function throwUnknownErrorException()
    {
        throw new WebapiException(
            __(AmazonServiceUnavailableException::ERROR_MESSAGE),
            0,
            WebapiException::HTTP_INTERNAL_ERROR
        );
    }

    protected function convertToMagentoAddress(array $address, $verifyCountry = false)
    {
        $amazonAddress  = $this->amazonAddressFactory->create(['address' => $address]);
        $magentoAddress = $this->addressHelper->convertToMagentoEntity($amazonAddress);

        if ($verifyCountry) {
            $countryCollection = $this->countryCollectionFactory->create();

            $collectionSize = $countryCollection->loadByStore()
                ->addFieldToFilter('country_id', ['eq' => $magentoAddress->getCountryId()])
                ->setPageSize(1)
                ->setCurPage(1)
                ->getSize();

            if (1 != $collectionSize) {
                throw new WebapiException(__('the country for your address is not allowed for this store'));
            }
        }

        return [$this->addressHelper->convertToArray($magentoAddress)];
    }

    protected function getOrderReferenceDetails($amazonOrderReferenceId, $addressConsentToken)
    {
        $client = $this->clientFactory->create();

        /**
         * @var ResponseInterface $response
         */
        $response = $client->getOrderReferenceDetails(
            [
                'amazon_order_reference_id' => $amazonOrderReferenceId,
                'address_consent_token'     => $addressConsentToken
            ]
        );

        $data = $response->toArray();

        if (200 != $data['ResponseStatus'] || ! isset($data['GetOrderReferenceDetailsResult'])) {
            throw new AmazonServiceUnavailableException();
        }

        return $data['GetOrderReferenceDetailsResult'];
    }

    protected function updateQuoteLink($amazonOrderReferenceId)
    {
        $quote     = $this->session->getQuote();
        $quoteLink = $this->quoteLinkFactory->create()->load($quote->getId(), 'quote_id');

        if ($quoteLink->getAmazonOrderReferenceId() != $amazonOrderReferenceId) {
            $quoteLink
                ->setAmazonOrderReferenceId($amazonOrderReferenceId)
                ->setQuoteId($quote->getId())
                ->setConfirmed(false)
                ->save();
        }
    }
}
