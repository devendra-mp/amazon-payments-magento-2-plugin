<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\AppInterface;
use Magento\Quote\Model\Quote;
use PayWithAmazon\ResponseInterface;

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
     * @var Data
     */
    protected $paymentHelper;

    public function __construct(
        Session $session,
        ClientFactoryInterface $clientFactory,
        Data $paymentHelper
    ) {
        $this->session          = $session;
        $this->clientFactory    = $clientFactory;
        $this->paymentHelper    = $paymentHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function saveOrderInformation($amazonOrderReferenceId)
    {
        $quote = $this->session->getQuote();

        $this->setReservedOrderId($quote);

        $data = [
            'amazon_order_reference_id' => $amazonOrderReferenceId,
            'amount'                    => $quote->getGrandTotal(),
            'currency_code'             => $quote->getQuoteCurrencyCode(),
            'seller_order_id'           => $quote->getReservedOrderId(),
            'store_name'                => $quote->getStore()->getName(),
            'custom_information'        =>
                'Magento Version : ' . AppInterface::VERSION . ' ' .
                'Plugin Version : ' . $this->paymentHelper->getModuleVersion()
            ,
            'platform_id'               => $this->paymentHelper->getMerchantId()
        ];
        
        /**
         * @var ResponseInterface $response
         */
        $response = $this->clientFactory->create()->setOrderReferenceDetails($data);

        return true;
    }

    protected function setReservedOrderId(Quote $quote)
    {
        if ( ! $quote->getReservedOrderId()) {
            $quote
                ->reserveOrderId()
                ->save();
        }
    }
}