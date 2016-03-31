<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Payment\Api\AddressManagementInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Helper\Address;
use Magento\Checkout\Model\Session;
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
    protected $address;

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
        Address $address,
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        Session $session
    ) {
        $this->clientFactory    = $clientFactory;
        $this->address          = $address;
        $this->quoteLinkFactory = $quoteLinkFactory;
        $this->session          = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function saveShippingAddress($amazonOrderReferenceId, $addressConsentToken)
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

        $amazonAddress = new AmazonAddress($response);
        $address       = $this->address->convertToMagentoEntity($amazonAddress);

        $quote = $this->session->getQuote();
        $this->updateQuoteLink($quote, $amazonOrderReferenceId);

        return [$this->address->convertToArray($address)];
    }

    protected function updateQuoteLink(Quote $quote, $amazonOrderReferenceId)
    {
        $quoteLink = $this->quoteLinkFactory
            ->create();

        $quoteLink
            ->load($quote->getId(), 'quote_id')
            ->setAmazonOrderReferenceId($amazonOrderReferenceId)
            ->setQuoteId($quote->getId())
            ->save();
    }
}