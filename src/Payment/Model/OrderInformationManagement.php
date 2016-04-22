<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Helper\Data as CoreHelper;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Helper\Data as PaymentHelper;
use Exception;
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
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var CoreHelper
     */
    protected $coreHelper;

    public function __construct(
        Session $session,
        ClientFactoryInterface $clientFactory,
        PaymentHelper $paymentHelper,
        CoreHelper $coreHelper
    ) {
        $this->session       = $session;
        $this->clientFactory = $clientFactory;
        $this->paymentHelper = $paymentHelper;
        $this->coreHelper    = $coreHelper;
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
            'platform_id'               => $this->coreHelper->getMerchantId()
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

    /**
     * {@inheritDoc}
     */
    public function confirmOrderReference($amazonOrderReferenceId)
    {
        try {
            /**
             * @var ResponseInterface $response
             */
            $response = $this->clientFactory->create()->confirmOrderReference(
                [
                    'amazon_order_reference_id' => $amazonOrderReferenceId
                ]
            );

            $data = $response->toArray();
            return (200 == $data['ResponseStatus']);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeOrderReference($amazonOrderReferenceId)
    {
        try {
            /**
             * @var ResponseInterface $response
             */
            $response = $this->clientFactory->create()->closeOrderReference(
                [
                    'amazon_order_reference_id' => $amazonOrderReferenceId
                ]
            );

            $data = $response->toArray();
            return (200 == $data['ResponseStatus']);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function cancelOrderReference($amazonOrderReferenceId)
    {
        try {
            /**
             * @var ResponseInterface $response
             */
            $response = $this->clientFactory->create()->cancelOrderReference(
                [
                    'amazon_order_reference_id' => $amazonOrderReferenceId
                ]
            );

            $data = $response->toArray();
            return (200 == $data['ResponseStatus']);
        } catch (Exception $e) {
            return false;
        }
    }
}
