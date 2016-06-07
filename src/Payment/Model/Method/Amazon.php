<?php
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\Payment\Model\Method;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonAuthorizationDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonAuthorizationResponseFactory;
use Amazon\Payment\Domain\AmazonAuthorizationStatus;
use Amazon\Payment\Domain\AmazonCaptureResponseFactory;
use Amazon\Payment\Domain\AmazonRefundResponseFactory;
use Amazon\Payment\Domain\Validator\AmazonAuthorization;
use Amazon\Payment\Domain\Validator\AmazonCapture;
use Amazon\Payment\Domain\Validator\AmazonPreCapture;
use Amazon\Payment\Domain\Validator\AmazonRefund;
use Amazon\Payment\Exception\AuthorizationExpiredException;
use Amazon\Payment\Exception\CapturePendingException;
use Amazon\Payment\Exception\SoftDeclineException;
use Amazon\Payment\Plugin\AdditionalInformation;
use Exception;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\ScopeInterface;

class Amazon extends AbstractMethod
{
    const PAYMENT_METHOD_CODE = 'amazon_payment';

    /**
     * {@inheritdoc}
     */
    protected $_isGateway = true;

    /**
     * {@inheritdoc}
     */
    protected $_code = self::PAYMENT_METHOD_CODE;

    /**
     * {@inheritdoc}
     */
    protected $_canCapture = true;

    /**
     * {@inheritdoc}
     */
    protected $_canAuthorize = true;

    /**
     * {@inheritdoc}
     */
    protected $_canRefund = true;

    /**
     * {@inheritdoc}
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * {@inheritdoc}
     */
    protected $_canUseInternal = false;

    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

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

    /**
     * @var AmazonPreCapture
     */
    protected $amazonPreCaptureValidator;

    /**
     * @var AmazonAuthorizationDetailsResponseFactory
     */
    protected $amazonAuthorizationDetailsResponseFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ClientFactoryInterface $clientFactory,
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        OrderInformationManagementInterface $orderInformationManagement,
        CartRepositoryInterface $cartRepository,
        AmazonAuthorizationResponseFactory $amazonAuthorizationResponseFactory,
        AmazonCaptureResponseFactory $amazonCaptureResponseFactory,
        AmazonRefundResponseFactory $amazonRefundResponseFactory,
        AmazonAuthorizationDetailsResponseFactory $amazonAuthorizationDetailsResponseFactory,
        AmazonAuthorization $amazonAuthorizationValidator,
        AmazonPreCapture $amazonPreCaptureValidator,
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

        $this->clientFactory                             = $clientFactory;
        $this->quoteLinkFactory                          = $quoteLinkFactory;
        $this->orderInformationManagement                = $orderInformationManagement;
        $this->cartRepository                            = $cartRepository;
        $this->amazonAuthorizationResponseFactory        = $amazonAuthorizationResponseFactory;
        $this->amazonCaptureResponseFactory              = $amazonCaptureResponseFactory;
        $this->amazonRefundResponseFactory               = $amazonRefundResponseFactory;
        $this->amazonAuthorizationValidator              = $amazonAuthorizationValidator;
        $this->amazonCaptureValidator                    = $amazonCaptureValidator;
        $this->amazonRefundValidator                     = $amazonRefundValidator;
        $this->paymentManagement                         = $paymentManagement;
        $this->amazonPreCaptureValidator                 = $amazonPreCaptureValidator;
        $this->amazonAuthorizationDetailsResponseFactory = $amazonAuthorizationDetailsResponseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        $this->authorizeInStore($payment, $amount, false);
    }

    /**
     * {@inheritdoc}
     */
    public function capture(InfoInterface $payment, $amount)
    {
        if ($payment->getParentTransactionId()) {
            $this->_capture($payment, $amount);
        } else {
            $this->authorizeInStore($payment, $amount, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function refund(InfoInterface $payment, $amount)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $captureId              = $payment->getParentTransactionId();
        $storeId                = $payment->getOrder()->getStoreId();

        $data = [
            'amazon_capture_id'   => $captureId,
            'refund_reference_id' => $amazonOrderReferenceId . '-R' . time(),
            'refund_amount'       => $amount,
            'currency_code'       => $this->getCurrencyCode($payment)
        ];

        $client = $this->clientFactory->create($storeId);

        $responseParser = $client->refund($data);
        $response       = $this->amazonRefundResponseFactory->create(['response' => $responseParser]);
        $this->amazonRefundValidator->validate($response);

        $payment->setTransactionId($response->getTransactionId());
    }

    protected function authorizeInStore(InfoInterface $payment, $amount, $capture = false)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $storeId                = $payment->getOrder()->getStoreId();

        try {
            $this->_authorize($payment, $amount, $amazonOrderReferenceId, $storeId, $capture);
        } catch (SoftDeclineException $e) {
            $this->processSoftDecline();
        } catch (Exception $e) {
            $this->processHardDecline($payment, $amazonOrderReferenceId);
        }
    }

    protected function reauthorizeAndCapture(
        InfoInterface $payment,
        $amount,
        $amazonOrderReferenceId,
        $authorizationId,
        $storeId
    ) {
        $this->paymentManagement->closeTransaction($authorizationId, $payment->getId(), $payment->getOrder()->getId());
        $payment->setParentTransactionId(null);
        $this->_authorize($payment, $amount, $amazonOrderReferenceId, $storeId, true);
    }

    protected function _authorize(InfoInterface $payment, $amount, $amazonOrderReferenceId, $storeId, $capture = false)
    {
        $data = [
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
            __('Unfortunately it is not possible to pay with Amazon for this order. Please choose another payment method.'),
            AmazonAuthorizationStatus::CODE_HARD_DECLINE,
            WebapiException::HTTP_FORBIDDEN
        );
    }

    protected function processSoftDecline()
    {
        throw new WebapiException(
            __('There has been a problem with the selected payment method on your Amazon account. Please choose another one.'),
            AmazonAuthorizationStatus::CODE_SOFT_DECLINE,
            WebapiException::HTTP_FORBIDDEN
        );
    }

    protected function _capture(InfoInterface $payment, $amount)
    {
        $amazonOrderReferenceId = $this->getAmazonOrderReferenceId($payment);
        $authorizationId        = $payment->getParentTransactionId();
        $storeId                = $payment->getOrder()->getStoreId();

        if ($this->validatePreCapture($payment, $amount, $amazonOrderReferenceId, $authorizationId, $storeId)) {
            $data = [
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
                $this->paymentManagement->queuePendingCapture($response, $payment->getId(), $payment->getOrder()->getId());
            } finally {
                if (isset($response)) {
                    $payment->setTransactionId($response->getTransactionId());
                }
            }
        }
    }

    protected function validatePreCapture(
        InfoInterface $payment,
        $amount,
        $amazonOrderReferenceId,
        $authorizationId,
        $storeId
    ) {
        try {
            $data = [
                'amazon_authorization_id' => $authorizationId,
            ];

            $client = $this->clientFactory->create($storeId);

            $responseParser = $client->getAuthorizationDetails($data);
            $response       = $this->amazonAuthorizationDetailsResponseFactory->create(['response' => $responseParser]);
            $this->amazonPreCaptureValidator->validate($response);

            return true;
        } catch (AuthorizationExpiredException $e) {
            $this->reauthorizeAndCapture($payment, $amount, $amazonOrderReferenceId, $authorizationId, $storeId);
        }

        return false;
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

    /**
     * {@inheritdoc}
     */
    public function assignData(DataObject $data)
    {
        $additionalData = $data->getAdditionalData();

        if ( ! is_array($additionalData)) {
            return $this;
        }

        $additionalData = new DataObject($additionalData);

        $infoInstance = $this->getInfoInstance();
        $key          = AdditionalInformation::KEY_SANDBOX_SIMULATION_REFERENCE;
        $infoInstance->setAdditionalInformation($key, $additionalData->getData($key));

        return $this;
    }
}
