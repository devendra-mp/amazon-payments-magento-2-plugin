<?php

namespace Amazon\Payment\Model\Method;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\UnexpectedDataException;
use Amazon\Core\Helper\Data as CoreHelper;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Domain\AmazonAuthorizationResponse;
use Amazon\Payment\Domain\AmazonAuthorizationStatus;
use Amazon\Payment\Domain\HardDeclineException;
use Amazon\Payment\Domain\SoftDeclineException;
use Exception;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception as WebapiException;
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

    /**
     * @var OrderInformationManagementInterface
     */
    protected $orderInformationManagement;

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

        $this->clientFactory              = $clientFactory;
        $this->coreHelper                 = $coreHelper;
        $this->quoteLinkFactory           = $quoteLinkFactory;
        $this->orderInformationManagement = $orderInformationManagement;
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
        $this->_eventManager->dispatch(
            'amazon_payment_authorize_before',
            ['context' => 'authorization', 'payment' => $payment, 'transport' => $transport]
        );
        $data = $transport->getData();

        $client = $this->clientFactory->create();

        try {
            $response = new AmazonAuthorizationResponse($client->authorize($data));

            $this->validateAuthorizationResponse($response);

            if ($capture) {
                $transactionId = $response->getCaptureTransactionId();
            } else {
                $transactionId = $response->getAuthorizeTransactionId();
                $payment->setIsTransactionClosed(false);
            }

            $payment->setTransactionId($transactionId);
        } catch (SoftDeclineException $e) {
            $this->processSoftDecline($payment, $amazonOrderReferenceId);
        } catch (Exception $e) {
            $this->processHardDecline($payment, $amazonOrderReferenceId);
        }
    }

    protected function validateAuthorizationResponse(AmazonAuthorizationResponse $response)
    {
        $status = $response->getStatus();

        switch ($status->getState()) {
            case AmazonAuthorizationStatus::STATE_OPEN:
            case AmazonAuthorizationStatus::STATE_PENDING:
                return true;
            case AmazonAuthorizationStatus::STATE_DECLINED:
                switch ($status->getReasonCode()) {
                    case AmazonAuthorizationStatus::REASON_AMAZON_REJECTED:
                    case AmazonAuthorizationStatus::REASON_TRANSACTION_TIMEOUT:
                    case AmazonAuthorizationStatus::REASON_PROCESSING_FAILURE:
                        throw new HardDeclineException();
                    case AmazonAuthorizationStatus::REASON_INVALID_PAYMENT_METHOD:
                        throw new SoftDeclineException();
                }
        }

        throw new UnexpectedDataException();
    }

    protected function processHardDecline(InfoInterface $payment, $amazonOrderReferenceId)
    {
        $this->orderInformationManagement->cancelOrderReference($amazonOrderReferenceId);
        $this->deleteAmazonOrderReferenceId($payment);

        throw new WebapiException(
            new Phrase(
                'Unfortunately it is not possible to pay with Amazon for this order, Please choose another payment method.'
            ),
            AmazonAuthorizationStatus::CODE_HARD_DECLINE,
            WebapiException::HTTP_FORBIDDEN
        );
    }

    protected function processSoftDecline(InfoInterface $payment, $amazonOrderReferenceId)
    {
        throw new WebapiException(
            new Phrase(
                'There has been a problem with the selected payment method on your Amazon account, please choose another one.'
            ),
            AmazonAuthorizationStatus::CODE_SOFT_DECLINE,
            WebapiException::HTTP_FORBIDDEN
        );
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
        $this->_eventManager->dispatch(
            'amazon_payment_capture_before',
            ['context' => 'capture', 'payment' => $payment, 'transport' => $transport]
        );
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
        return $this->getQuoteLink($payment)->getAmazonOrderReferenceId();
    }

    protected function deleteAmazonOrderReferenceId(InfoInterface $payment)
    {
        $this->getQuoteLink($payment)->delete();
    }

    protected function getQuoteLink(InfoInterface $payment)
    {
        $quoteId   = $payment->getOrder()->getQuoteId();
        $quoteLink = $this->quoteLinkFactory->create();
        $quoteLink->load($quoteId, 'quote_id');

        return $quoteLink;
    }
}
