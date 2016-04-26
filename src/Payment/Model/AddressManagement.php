<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Payment\Api\AddressManagementInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Helper\Address;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Phrase;
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

    public function __construct(
        ClientFactoryInterface $clientFactory,
        Address $addressHelper,
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        Session $session
    ) {
        $this->clientFactory    = $clientFactory;
        $this->addressHelper    = $addressHelper;
        $this->quoteLinkFactory = $quoteLinkFactory;
        $this->session          = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getShippingAddress($amazonOrderReferenceId, $addressConsentToken)
    {
        try {
            $data = $this->getOrderReferenceDetails($amazonOrderReferenceId, $addressConsentToken);

            $this->updateQuoteLink($amazonOrderReferenceId);

            if (isset($data['OrderReferenceDetails']['Destination']['PhysicalDestination'])) {
                $shippingAddress = $data['OrderReferenceDetails']['Destination']['PhysicalDestination'];

                return $this->convertToMagentoAddress($shippingAddress);
            }

            throw new ValidatorException(new Phrase('address not found'));
        } catch (Exception $e) {
            $this->throwUnknownErrorException();
        }
    }

    /**
     * {@inheritDoc}
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

            throw new ValidatorException(new Phrase('address not found'));
        } catch (Exception $e) {
            $this->throwUnknownErrorException();
        }
    }

    protected function throwUnknownErrorException()
    {
        throw new WebapiException(new Phrase('an unknown error occurred'), 0, WebapiException::HTTP_INTERNAL_ERROR);
    }

    protected function convertToMagentoAddress($address)
    {
        $amazonAddress  = new AmazonAddress($address);
        $magentoAddress = $this->addressHelper->convertToMagentoEntity($amazonAddress);

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
        $quoteLink = $this->quoteLinkFactory
            ->create();

        $quoteLink
            ->load($quote->getId(), 'quote_id');

        if ($quoteLink->getAmazonOrderReferenceId() != $amazonOrderReferenceId) {
            $quoteLink
                ->setAmazonOrderReferenceId($amazonOrderReferenceId)
                ->setQuoteId($quote->getId())
                ->setConfirmed(false)
                ->save();
        }
    }
}