<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Payment\Api\AddressManagementInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Helper\Address;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use PayWithAmazon\ResponseInterface;
use UnexpectedValueException;

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

        } catch (Exception $e) {
        }

        return null;
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

        } catch (Exception $e) {
        }

        return null;
    }

    protected function convertToMagentoAddress($address)
    {
        $amazonAddress   = new AmazonAddress($address);
        $magentoAddress  = $this->addressHelper->convertToMagentoEntity($amazonAddress);

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

        if (200 == $data['ResponseStatus']) {
            throw new UnexpectedValueException();
        }

        if ( ! isset($data['GetOrderReferenceDetailsResult'])) {
            throw new UnexpectedValueException();
        }

        return $data['GetOrderReferenceDetailsResult'];
    }

    protected function updateQuoteLink($amazonOrderReferenceId)
    {
        $quote     = $this->session->getQuote();
        $quoteLink = $this->quoteLinkFactory
            ->create();

        $quoteLink
            ->load($quote->getId(), 'quote_id')
            ->setAmazonOrderReferenceId($amazonOrderReferenceId)
            ->setQuoteId($quote->getId())
            ->save();
    }
}