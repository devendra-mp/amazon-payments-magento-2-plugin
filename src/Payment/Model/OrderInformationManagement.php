<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Magento\Checkout\Model\Session;
use PayWithAmazon\ResponseInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;

class OrderInformationManagement implements OrderInformationManagementInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var QuoteLinkInterfaceFactory
     */
    private $quoteLinkFactory;

    public function __construct(
        Session $session,
        ClientFactoryInterface $clientFactory,
        QuoteLinkInterfaceFactory $quoteLinkFactory
    ) {
        $this->session          = $session;
        $this->clientFactory    = $clientFactory;
        $this->quoteLinkFactory = $quoteLinkFactory;
    }

    public function saveOrderInformation($amazonOrderReferenceId)
    {
        $quote = $this->session->getQuote();

        /**
         * @var ResponseInterface $response
         */
        $response = $this->clientFactory->create()->setOrderReferenceDetails(
            [
                'amazon_order_reference_id' => $amazonOrderReferenceId,
                'amount'                    => $quote->getGrandTotal(),
                'currency_code'             => $quote->getQuoteCurrencyCode()
            ]
        );

        $this->updateLink($quote->getId(), $amazonOrderReferenceId);

        return true;
    }

    public function updateLink($quoteId, $amazonOrderReferenceId)
    {
        $quoteLink = $this->quoteLinkFactory
            ->create();

        $quoteLink
            ->load($quoteId)
            ->setAmazonOrderReferenceId($amazonOrderReferenceId)
            ->setQuoteId($quoteId)
            ->save();
    }
}