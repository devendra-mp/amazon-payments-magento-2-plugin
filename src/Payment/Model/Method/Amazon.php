<?php

namespace Amazon\Payment\Model\Method;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Core\Helper\Data as CoreHelper;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use PayWithAmazon\ResponseParser;

class Amazon extends AbstractMethod
{
    const PAYMENT_METHOD_CODE = 'amazon_payment';

    /**
     * {@inheritDoc}
     */
    protected $_code = self::PAYMENT_METHOD_CODE;

    /**
     * {@inheritDoc}
     */
    protected $_canCapture = true;

    /**
     * {@inheritDoc}
     */
    protected $_canAuthorize = true;

    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var CoreHelper
     */
    protected $coreHelper;

    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ClientFactoryInterface $clientFactory,
        CoreHelper $coreHelper,
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->clientFactory       = $clientFactory;
        $this->coreHelper          = $coreHelper;
        $this->quoteLinkFactory    = $quoteLinkFactory;
    }

    /**
     * @param Payment $payment
     * @param float   $amount
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        $this->_authorize($payment, $amount, false);
    }

    /**
     * @param Payment $payment
     * @param float   $amount
     */
    public function capture(InfoInterface $payment, $amount)
    {
        if ($payment->getParentTransactionId()) {
            $this->_capture($payment, $amount);
        } else {
            $this->_authorize($payment, $amount, true);
        }
    }

    protected function _authorize(InfoInterface $payment, $amount, $capture = false)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);

        $data = [
            'merchant_id'                => $this->coreHelper->getMerchantId(),
            'amazon_order_reference_id'  => $amazonOrderReferenceId,
            'authorization_amount'       => $amount,
            'currency_code'              => $this->getCurrencyCode($payment),
            'authorization_reference_id' => $amazonOrderReferenceId . '-AUTH',
            'capture_now'                => $capture,
            'transaction_timeout'        => 0
        ];

        $transport = new DataObject($data);
        $this->_eventManager->dispatch('amazon_payment_authorize_before', ['context' => 'authorization', 'payment' => $payment, 'transport' => $transport]);
        $data = $transport->getData();

        $client = $this->clientFactory->create();
        /**
         * @var ResponseParser $response
         */
        $response = $client->authorize($data);
        $responseData = $response->toArray();

        if ($capture) {
            $transactionId = $responseData['AuthorizeResult']['AuthorizationDetails']['IdList']['member'];
        } else {
            $transactionId = $responseData['AuthorizeResult']['AuthorizationDetails']['AmazonAuthorizationId'];
            $payment->setIsTransactionClosed(false);
        }

        $payment->setTransactionId($transactionId);
    }

    protected function _capture(InfoInterface $payment, $amount)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $authorizationId        = $payment->getParentTransactionId();

        $data = [
            'merchant_id'             => $this->coreHelper->getMerchantId(),
            'amazon_authorization_id' => $authorizationId,
            'capture_amount'          => $amount,
            'currency_code'           => $this->getCurrencyCode($payment),
            'capture_reference_id'    => $amazonOrderReferenceId . '-CAP'
        ];

        $transport = new DataObject($data);
        $this->_eventManager->dispatch('amazon_payment_capture_before', ['context' => 'capture', 'payment' => $payment, 'transport' => $transport]);
        $data = $transport->getData();

        $client = $this->clientFactory->create();
        /**
         * @var ResponseParser $response
         */
        $response     = $client->capture($data);
        $responseData = $response->toArray();

        $transactionId = $responseData['CaptureResult']['CaptureDetails']['AmazonCaptureId'];

        $payment->setTransactionId($transactionId);
    }

    protected function getCurrencyCode(InfoInterface $payment)
    {
        return $payment->getOrder()->getOrderCurrencyCode();
    }

    protected function getAmazonOrderReferenceId(InfoInterface $payment)
    {
        $quoteId = $payment->getOrder()->getQuoteId();
        $quote   = $this->quoteLinkFactory->create();
        $quote->load($quoteId, 'quote_id');

        return $quote->getAmazonOrderReferenceId();
    }
}
