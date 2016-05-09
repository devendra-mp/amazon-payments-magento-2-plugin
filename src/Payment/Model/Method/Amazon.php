<?php

namespace Amazon\Payment\Model\Method;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Helper\Data as CoreHelper;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonAuthorizationResponseFactory;
use Amazon\Payment\Domain\AmazonAuthorizationStatus;
use Amazon\Payment\Domain\AmazonCaptureResponseFactory;
use Amazon\Payment\Domain\AmazonRefundResponseFactory;
use Amazon\Payment\Domain\Validator\AmazonAuthorization;
use Amazon\Payment\Domain\Validator\AmazonCapture;
use Amazon\Payment\Domain\Validator\AmazonRefund;
use Amazon\Payment\Exception\CapturePendingException;
use Amazon\Payment\Exception\SoftDeclineException;
use Exception;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Store\Model\ScopeInterface;
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
     * {@inheritDoc}
     */
    protected $_canRefund = true;

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

    /**
     * @var OrderInformationManagementInterface
     */
    protected $orderInformationManagement;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var AmazonAuthorizationResponseFactory
     */
    protected $amazonAuthorizationResponseFactory;

    /**
     * @var AmazonRefundResponseFactory
     */
    protected $amazonRefundResponseFactory;

    /**
     * @var AmazonCaptureResponseFactory
     */
    protected $amazonCaptureResponseFactory;

    /**
     * @var AmazonAuthorization
     */
    protected $amazonAuthorizationValidator;

    /**
     * @var AmazonCapture
     */
    protected $amazonCaptureValidator;

    /**
     * @var AmazonRefund
     */
    protected $amazonRefundValidator;

    /**
     * @var PaymentManagementInterface
     */
    protected $paymentManagement;

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
        OrderInformationManagementInterface $orderInformationManagement,
        CartRepositoryInterface $cartRepository,
        AmazonAuthorizationResponseFactory $amazonAuthorizationResponseFactory,
        AmazonCaptureResponseFactory $amazonCaptureResponseFactory,
        AmazonRefundResponseFactory $amazonRefundResponseFactory,
        AmazonAuthorization $amazonAuthorizationValidator,
        AmazonCapture $amazonCaptureValidator,
        AmazonRefund $amazonRefundValidator,
        PaymentManagementInterface $paymentManagement,
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

        $this->clientFactory                      = $clientFactory;
        $this->coreHelper                         = $coreHelper;
        $this->quoteLinkFactory                   = $quoteLinkFactory;
        $this->orderInformationManagement         = $orderInformationManagement;
        $this->cartRepository                     = $cartRepository;
        $this->amazonAuthorizationResponseFactory = $amazonAuthorizationResponseFactory;
        $this->amazonCaptureResponseFactory       = $amazonCaptureResponseFactory;
        $this->amazonRefundResponseFactory        = $amazonRefundResponseFactory;
        $this->amazonAuthorizationValidator       = $amazonAuthorizationValidator;
        $this->amazonCaptureValidator             = $amazonCaptureValidator;
        $this->amazonRefundValidator              = $amazonRefundValidator;
        $this->paymentManagement                  = $paymentManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        $this->_authorize($payment, $amount, false);
    }

    /**
     * {@inheritDoc}
     */
    public function capture(InfoInterface $payment, $amount)
    {
        if ($payment->getParentTransactionId()) {
            $this->_capture($payment, $amount);
        } else {
            $this->_authorize($payment, $amount, true);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function refund(InfoInterface $payment, $amount)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $captureId              = $payment->getParentTransactionId();
        $storeId                = $payment->getOrder()->getStoreId();

        $data = [
            'merchant_id'         => $this->coreHelper->getMerchantId(ScopeInterface::SCOPE_STORE, $storeId),
            'amazon_capture_id'   => $captureId,
            'refund_reference_id' => $amazonOrderReferenceId . '-R' . time(),
            'refund_amount'       => $amount,
            'currency_code'       => $this->getCurrencyCode($payment)
        ];

        $client = $this->clientFactory->create($storeId);

        $responseParser = $client->refund($data);
        $response       = $this->amazonRefundResponseFactory->create(['response' => $responseParser]);
        $this->amazonRefundValidator->validate($response);
    }

    protected function _authorize(InfoInterface $payment, $amount, $capture = false)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $storeId                = $payment->getOrder()->getStoreId();

        $data = [
            'merchant_id'                => $this->coreHelper->getMerchantId(ScopeInterface::SCOPE_STORE, $storeId),
            'amazon_order_reference_id'  => $amazonOrderReferenceId,
            'authorization_amount'       => $amount,
            'currency_code'              => $this->getCurrencyCode($payment),
            'authorization_reference_id' => $amazonOrderReferenceId . '-A' . time(),
            'capture_now'                => $capture,
            'transaction_timeout'        => 0
        ];

        $transport = new DataObject($data);
        $this->_eventManager->dispatch(
            'amazon_payment_authorize_before',
            [
                'context'   => ($capture) ? 'authorization_capture' : 'authorization',
                'payment'   => $payment,
                'transport' => $transport
            ]
        );
        $data = $transport->getData();

        $client = $this->clientFactory->create($storeId);

        try {
            $responseParser = $client->authorize($data);
            $response       = $this->amazonAuthorizationResponseFactory->create(['response' => $responseParser]);

            $this->amazonAuthorizationValidator->validate($response);

            if ($capture) {
                $transactionId = $response->getCaptureTransactionId();
            } else {
                $transactionId = $response->getAuthorizeTransactionId();
                $payment->setIsTransactionClosed(false);
            }

            $payment->setTransactionId($transactionId);
        } catch (SoftDeclineException $e) {
            $this->processSoftDecline();
        } catch (Exception $e) {
            $this->processHardDecline($payment, $amazonOrderReferenceId);
        }
    }

    protected function processHardDecline(InfoInterface $payment, $amazonOrderReferenceId)
    {
        $storeId = $payment->getOrder()->getStoreId();

        try {
            $this->orderInformationManagement->cancelOrderReference($amazonOrderReferenceId, $storeId);
        } catch (Exception $e) {
            //ignored as it's likely in a cancelled state already or there is a problem we cannot rectify
        }

        $this->deleteAmazonOrderReferenceId($payment);
        $this->reserveNewOrderId($payment);

        throw new WebapiException(
            __('Unfortunately it is not possible to pay with Amazon for this order, Please choose another payment method.'),
            AmazonAuthorizationStatus::CODE_HARD_DECLINE,
            WebapiException::HTTP_FORBIDDEN
        );
    }

    protected function processSoftDecline()
    {
        throw new WebapiException(
            __('There has been a problem with the selected payment method on your Amazon account, please choose another one.'),
            AmazonAuthorizationStatus::CODE_SOFT_DECLINE,
            WebapiException::HTTP_FORBIDDEN
        );
    }

    protected function _capture(InfoInterface $payment, $amount)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $authorizationId        = $payment->getParentTransactionId();
        $storeId                = $payment->getOrder()->getStoreId();

        $data = [
            'merchant_id'             => $this->coreHelper->getMerchantId(ScopeInterface::SCOPE_STORE, $storeId),
            'amazon_authorization_id' => $authorizationId,
            'capture_amount'          => $amount,
            'currency_code'           => $this->getCurrencyCode($payment),
            'capture_reference_id'    => $amazonOrderReferenceId . '-C' . time()
        ];

        $transport = new DataObject($data);
        $this->_eventManager->dispatch(
            'amazon_payment_capture_before',
            ['context' => 'capture', 'payment' => $payment, 'transport' => $transport]
        );
        $data = $transport->getData();

        $client = $this->clientFactory->create($storeId);

        try {
            $responseParser = $client->capture($data);
            $response       = $this->amazonCaptureResponseFactory->create(['response' => $responseParser]);

            $this->amazonCaptureValidator->validate($response);
        } catch (CapturePendingException $e) {
            $payment->setIsTransactionPending(true);
            $payment->setIsTransactionClosed(false);
            $this->paymentManagement->queuePendingCapture($response);
        } finally {
            $payment->setTransactionId($response->getTransactionId());
        }
    }
    
    protected function getCurrencyCode(InfoInterface $payment)
    {
        return $payment->getOrder()->getOrderCurrencyCode();
    }

    protected function getAmazonOrderReferenceId(InfoInterface $payment)
    {
        return $this->getQuoteLink($payment)->getAmazonOrderReferenceId();
    }

    protected function deleteAmazonOrderReferenceId(InfoInterface $payment)
    {
        $this->getQuoteLink($payment)->delete();
    }

    protected function reserveNewOrderId(InfoInterface $payment)
    {
        $this->getQuote($payment)
            ->setReservedOrderId(null)
            ->reserveOrderId()
            ->save();
    }

    protected function getQuote(InfoInterface $payment)
    {
        $quoteId = $payment->getOrder()->getQuoteId();
        return $this->cartRepository->get($quoteId);
    }

    protected function getQuoteLink(InfoInterface $payment)
    {
        $quoteId   = $payment->getOrder()->getQuoteId();
        $quoteLink = $this->quoteLinkFactory->create();
        $quoteLink->load($quoteId, 'quote_id');

        return $quoteLink;
    }
}
