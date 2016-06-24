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
namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Helper\Data as AmazonCoreHelper;
use Amazon\Core\Model\Config\Source\PaymentAction;
use Amazon\Payment\Api\Data\PendingAuthorizationInterface;
use Amazon\Payment\Api\Data\PendingAuthorizationInterfaceFactory;
use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\Data\PendingCaptureInterfaceFactory;
use Amazon\Payment\Api\Data\PendingRefundInterfaceFactory;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonAuthorizationDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Amazon\Payment\Domain\AmazonGetOrderDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonOrderStatus;
use Amazon\Payment\Domain\Details\AmazonAuthorizationDetails;
use Amazon\Payment\Domain\Details\AmazonCaptureDetails;
use Amazon\Payment\Domain\Details\AmazonOrderDetails;
use Amazon\Payment\Domain\Details\AmazonRefundDetails;
use Amazon\Payment\Domain\Validator\AmazonAuthorization;
use Amazon\Payment\Exception\SoftDeclineException;
use Exception;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Payment\Model\InfoInterface as PaymentInfoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Store\Model\ScopeInterface;

class PaymentManagement implements PaymentManagementInterface
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var PendingCaptureInterfaceFactory
     */
    protected $pendingCaptureFactory;

    /**
     * @var AmazonCaptureDetailsResponseFactory
     */
    protected $amazonCaptureDetailsResponseFactory;

    /**
     * @var NotifierInterface
     */
    protected $notifier;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * @var PendingAuthorizationInterfaceFactory
     */
    protected $pendingAuthorizationFactory;

    /**
     * @var AmazonAuthorizationDetailsResponseFactory
     */
    protected $amazonAuthorizationDetailsResponseFactory;

    /**
     * @var AmazonAuthorization
     */
    protected $amazonAuthorizationValidator;

    /**
     * @var PendingRefundInterfaceFactory
     */
    protected $pendingRefundFactory;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var AmazonCoreHelper
     */
    protected $amazonCoreHelper;

    /**
     * @var AmazonGetOrderDetailsResponseFactory
     */
    protected $amazonGetOrderDetailsResponseFactory;

    /**
     * PaymentManagement constructor.
     *
     * @param PendingCaptureInterfaceFactory            $pendingCaptureFactory
     * @param PendingAuthorizationInterfaceFactory      $pendingAuthorizationFactory
     * @param ClientFactoryInterface                    $clientFactory
     * @param AmazonCaptureDetailsResponseFactory       $amazonCaptureDetailsResponseFactory
     * @param AmazonAuthorizationDetailsResponseFactory $amazonAuthorizationDetailsResponseFactory
     * @param AmazonAuthorization                       $amazonAuthorizationValidator
     * @param NotifierInterface                         $notifier
     * @param UrlInterface                              $urlBuilder
     * @param SearchCriteriaBuilderFactory              $searchCriteriaBuilderFactory
     * @param OrderPaymentRepositoryInterface           $orderPaymentRepository
     * @param OrderRepositoryInterface                  $orderRepository
     * @param TransactionRepositoryInterface            $transactionRepository
     * @param InvoiceRepositoryInterface                $invoiceRepository
     * @param PendingRefundInterfaceFactory             $pendingRefundFactory
     * @param ManagerInterface                          $eventManager
     * @param AmazonCoreHelper                          $amazonCoreHelper
     * @param AmazonGetOrderDetailsResponseFactory      $amazonGetOrderDetailsResponseFactory
     */
    public function __construct(
        PendingCaptureInterfaceFactory $pendingCaptureFactory,
        PendingAuthorizationInterfaceFactory $pendingAuthorizationFactory,
        ClientFactoryInterface $clientFactory,
        AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory,
        AmazonAuthorizationDetailsResponseFactory $amazonAuthorizationDetailsResponseFactory,
        AmazonAuthorization $amazonAuthorizationValidator,
        NotifierInterface $notifier,
        UrlInterface $urlBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        OrderRepositoryInterface $orderRepository,
        TransactionRepositoryInterface $transactionRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        PendingRefundInterfaceFactory $pendingRefundFactory,
        ManagerInterface $eventManager,
        AmazonCoreHelper $amazonCoreHelper,
        AmazonGetOrderDetailsResponseFactory $amazonGetOrderDetailsResponseFactory
    ) {
        $this->clientFactory                             = $clientFactory;
        $this->pendingCaptureFactory                     = $pendingCaptureFactory;
        $this->pendingAuthorizationFactory               = $pendingAuthorizationFactory;
        $this->amazonCaptureDetailsResponseFactory       = $amazonCaptureDetailsResponseFactory;
        $this->amazonAuthorizationDetailsResponseFactory = $amazonAuthorizationDetailsResponseFactory;
        $this->amazonAuthorizationValidator              = $amazonAuthorizationValidator;
        $this->notifier                                  = $notifier;
        $this->urlBuilder                                = $urlBuilder;
        $this->searchCriteriaBuilderFactory              = $searchCriteriaBuilderFactory;
        $this->orderPaymentRepository                    = $orderPaymentRepository;
        $this->orderRepository                           = $orderRepository;
        $this->transactionRepository                     = $transactionRepository;
        $this->invoiceRepository                         = $invoiceRepository;
        $this->pendingRefundFactory                      = $pendingRefundFactory;
        $this->eventManager                              = $eventManager;
        $this->amazonCoreHelper                          = $amazonCoreHelper;
        $this->amazonGetOrderDetailsResponseFactory      = $amazonGetOrderDetailsResponseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAuthorization(
        $pendingAuthorizationId,
        AmazonAuthorizationDetails $authorizationDetails = null,
        AmazonOrderDetails $orderDetails = null
    ) {
        try {
            $pendingAuthorization = $this->pendingAuthorizationFactory->create();
            $pendingAuthorization->getResource()->beginTransaction();
            $pendingAuthorization->setLockOnLoad(true);
            $pendingAuthorization->load($pendingAuthorizationId);

            if ($pendingAuthorization->getOrderId()) {
                if ($pendingAuthorization->isProcessed()) {
                    $this->processNewAuthorization($pendingAuthorization, $orderDetails);
                } else {
                    $this->processUpdateAuthorization($pendingAuthorization, $authorizationDetails);
                }
            }

            $pendingAuthorization->getResource()->commit();
        } catch (Exception $e) {
            $pendingAuthorization->getResource()->rollBack();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateCapture($pendingCaptureId, AmazonCaptureDetails $captureDetails = null)
    {
        try {
            $pendingCapture = $this->pendingCaptureFactory->create();
            $pendingCapture->getResource()->beginTransaction();
            $pendingCapture->setLockOnLoad(true);
            $pendingCapture->load($pendingCaptureId);

            if ($pendingCapture->getCaptureId()) {
                $order   = $this->orderRepository->get($pendingCapture->getOrderId());
                $payment = $this->orderPaymentRepository->get($pendingCapture->getPaymentId());
                $order->setPayment($payment);
                $order->setData(OrderInterface::PAYMENT, $payment);

                if (null === $captureDetails) {
                    $responseParser = $this->clientFactory->create($order->getStoreId())->getCaptureDetails([
                        'amazon_capture_id' => $pendingCapture->getCaptureId()
                    ]);

                    $response       = $this->amazonCaptureDetailsResponseFactory->create(['response' => $responseParser]);
                    $captureDetails = $response->getDetails();
                }

                $this->processUpdateCaptureResponse($captureDetails, $pendingCapture, $payment, $order);
            }

            $pendingCapture->getResource()->commit();
        } catch (Exception $e) {
            $pendingCapture->getResource()->rollBack();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingCapture(AmazonCaptureDetails $details, $paymentId, $orderId)
    {
        $this->pendingCaptureFactory->create()
            ->setCaptureId($details->getTransactionId())
            ->setPaymentId($paymentId)
            ->setOrderId($orderId)
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingAuthorization(AmazonAuthorizationDetails $details, OrderInterface $order)
    {
        $pendingAuthorization = $this->pendingAuthorizationFactory->create()
            ->setAuthorizationId($details->getAuthorizeTransactionId());

        $order->addRelatedObject($pendingAuthorization);
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingRefund(AmazonRefundDetails $details, PaymentInfoInterface $payment)
    {
        $this->pendingRefundFactory->create()
            ->setRefundId($details->getRefundId())
            ->setPaymentId($payment->getId())
            ->setOrderId($payment->getOrder()->getId())
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function closeTransaction($transactionId, PaymentInfoInterface $payment, OrderInterface $order)
    {
        $this->getTransaction($transactionId, $payment, $order)->setIsClosed(1)->save();
    }

    protected function processUpdateAuthorization(
        PendingAuthorizationInterface $pendingAuthorization,
        AmazonAuthorizationDetails $authorizationDetails = null
    ) {
        $order   = $this->orderRepository->get($pendingAuthorization->getOrderId());
        $payment = $this->orderPaymentRepository->get($pendingAuthorization->getPaymentId());
        $order->setPayment($payment);
        $order->setData(OrderInterface::PAYMENT, $payment);

        $authorizationId = $pendingAuthorization->getAuthorizationId();

        if (null === $authorizationDetails) {
            $responseParser = $this->clientFactory->create($order->getStoreId())->getAuthorizationDetails([
                'amazon_authorization_id' => $authorizationId
            ]);

            $response             = $this->amazonAuthorizationDetailsResponseFactory->create(['response' => $responseParser]);
            $authorizationDetails = $response->getDetails();
        }

        $capture = $authorizationDetails->hasCapture();

        try {
            $this->amazonAuthorizationValidator->validate($authorizationDetails);

            if ( ! $authorizationDetails->isPending()) {
                $this->completePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
            }
        } catch (SoftDeclineException $e) {
            $this->softDeclinePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
        } catch (\Exception $e) {
            $this->hardDeclinePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
        }
    }

    protected function completePendingAuthorization(
        OrderInterface $order,
        OrderPaymentInterface $payment,
        PendingAuthorizationInterface $pendingAuthorization,
        $capture,
        TransactionInterface $newTransaction = null
    ) {
        $authorizationId = $pendingAuthorization->getAuthorizationId();

        $this->setProcessing($order);

        if ($capture) {
            $invoice = $this->getInvoiceAndSetPaid($authorizationId, $order);

            if ( ! $newTransaction) {
                $this->closeTransaction($authorizationId, $payment, $order);
            } else {
                $invoice->setTransactionId($newTransaction->getTxnId());
            }

            $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
            $message         = __('Captured amount of %1 online', $formattedAmount);
            $payment->setDataUsingMethod(
                'base_amount_paid_online',
                $payment->formatAmount($invoice->getBaseGrandTotal())
            );
        } else {
            $formattedAmount = $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized());
            $message         = __('Authorized amount of %1 online', $formattedAmount);
        }

        $transaction = ($newTransaction) ?: $this->getTransaction($authorizationId, $payment, $order);
        $payment->addTransactionCommentsToOrder($transaction, $message);

        $order->save();
        $pendingAuthorization->delete();
    }

    protected function softDeclinePendingAuthorization(
        OrderInterface $order,
        OrderPaymentInterface $payment,
        PendingAuthorizationInterface $pendingAuthorization,
        $capture
    ) {
        $authorizationId = $pendingAuthorization->getAuthorizationId();

        if ($capture) {
            $invoice = $this->getInvoice($authorizationId, $order);
            $this->setPaymentReview($order);
            $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        } else {
            $formattedAmount = $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        }

        $transaction = $this->getTransaction($authorizationId, $payment, $order);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $this->closeTransaction($authorizationId, $payment, $order);

        $pendingAuthorization->setProcessed(true);
        $pendingAuthorization->save();
        $order->save();

        $this->eventManager->dispatch(
            'amazon_payment_pending_authorization_soft_decline_after',
            [
                'order'                => $order,
                'pendingAuthorization' => $pendingAuthorization,
            ]
        );
    }

    protected function hardDeclinePendingAuthorization(
        OrderInterface $order,
        OrderPaymentInterface $payment,
        PendingAuthorizationInterface $pendingAuthorization,
        $capture
    ) {
        $authorizationId = $pendingAuthorization->getAuthorizationId();

        if ($capture) {
            $invoice         = $this->getInvoiceAndSetCancelled($authorizationId, $order);
            $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
            $message         = __('Declined amount of %1 online', $formattedAmount);
            $this->addCaptureDeclinedNotice($order);
        } else {
            $formattedAmount = $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        }

        $this->setOnHold($order);

        $transaction = $this->getTransaction($authorizationId, $payment, $order);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $this->closeTransaction($authorizationId, $payment, $order);

        $pendingAuthorization->delete();
        $order->save();

        $this->eventManager->dispatch(
            'amazon_payment_pending_authorization_hard_decline_after',
            [
                'order'                => $order,
                'pendingAuthorization' => $pendingAuthorization,
            ]
        );
    }

    protected function processNewAuthorization(
        PendingAuthorizationInterface $pendingAuthorization,
        AmazonOrderDetails $orderDetails = null
    ) {
        $order   = $this->orderRepository->get($pendingAuthorization->getOrderId());
        $payment = $this->orderPaymentRepository->get($pendingAuthorization->getPaymentId());
        $order->setPayment($payment);
        $order->setData(OrderInterface::PAYMENT, $payment);

        $storeId = $order->getStoreId();

        if (null === $orderDetails) {
            $responseParser = $this->clientFactory->create($storeId)->getOrderReferenceDetails([
                'amazon_order_reference_id' => $order->getExtensionAttributes()->getAmazonOrderReferenceId()
            ]);

            $response     = $this->amazonGetOrderDetailsResponseFactory->create(['response' => $responseParser]);
            $orderDetails = $response->getDetails();
        }

        if (AmazonOrderStatus::STATE_OPEN == $orderDetails->getStatus()->getState()) {
            $paymentAction = $this->amazonCoreHelper->getPaymentAction(ScopeInterface::SCOPE_STORE, $storeId);
            $capture       = (PaymentAction::AUTHORIZE_AND_CAPTURE === $paymentAction);

            if ($capture) {
                $this->requestNewAuthorizationAndCapture($order, $payment, $pendingAuthorization);
            } else {
                $this->requestNewAuthorization($order, $payment, $pendingAuthorization);
            }
        }
    }

    protected function requestNewAuthorization(
        OrderInterface $order,
        OrderPaymentInterface $payment,
        PendingAuthorizationInterface $pendingAuthorization
    ) {
        $capture = false;

        try {
            $baseAmount = $payment->formatAmount($payment->getBaseAmountAuthorized());

            $method = $payment->getMethodInstance();
            $method->setStore($order->getStoreId());
            $method->authorizeInCron($payment, $baseAmount, $capture);

            $transaction = $payment->addTransaction(Transaction::TYPE_AUTH);

            $this->completePendingAuthorization(
                $order,
                $payment,
                $pendingAuthorization,
                $capture,
                $transaction
            );
        } catch (SoftDeclineException $e) {
            $this->softDeclinePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
        } catch (\Exception $e) {
            $this->hardDeclinePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
        }
    }

    protected function requestNewAuthorizationAndCapture(
        OrderInterface $order,
        OrderPaymentInterface $payment,
        PendingAuthorizationInterface $pendingAuthorization
    ) {
        $capture = true;

        try {
            $invoice = $this->getInvoice($payment->getLastTransId(), $order);

            $baseAmount = $payment->formatAmount($invoice->getBaseGrandTotal());

            $method = $payment->getMethodInstance();
            $method->setStore($order->getStoreId());
            $method->authorizeInCron($payment, $baseAmount, $capture);

            $transaction = $payment->addTransaction(Transaction::TYPE_CAPTURE, $invoice, true);

            $this->completePendingAuthorization(
                $order,
                $payment,
                $pendingAuthorization,
                $capture,
                $transaction
            );
        } catch (SoftDeclineException $e) {
            $this->softDeclinePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
        } catch (\Exception $e) {
            $this->hardDeclinePendingAuthorization($order, $payment, $pendingAuthorization, $capture);
        }
    }

    protected function processUpdateCaptureResponse(
        AmazonCaptureDetails $details,
        PendingCaptureInterface $pendingCapture,
        OrderPaymentInterface $payment,
        OrderInterface $order
    ) {
        $status = $details->getStatus();

        switch ($status->getState()) {
            case AmazonCaptureStatus::STATE_COMPLETED:
                $this->completePendingCapture($pendingCapture, $payment, $order);
                break;
            case AmazonCaptureStatus::STATE_DECLINED:
                $this->declinePendingCapture($pendingCapture, $payment, $order);
                break;
        }
    }

    protected function completePendingCapture(
        PendingCaptureInterface $pendingCapture,
        OrderPaymentInterface $payment,
        OrderInterface $order
    ) {
        $transactionId   = $pendingCapture->getCaptureId();
        $transaction     = $this->getTransaction($transactionId, $payment, $order);
        $invoice         = $this->getInvoice($transactionId, $order);
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Captured amount of %1 online', $formattedAmount);

        $this->getInvoiceAndSetPaid($transactionId, $order);
        $payment->setDataUsingMethod('base_amount_paid_online', $invoice->getBaseGrandTotal());
        $this->setProcessing($order);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $order->save();

        $this->closeTransaction($transactionId, $payment, $order);
        $pendingCapture->delete();
    }

    protected function declinePendingCapture(
        PendingCaptureInterface $pendingCapture,
        OrderPaymentInterface $payment,
        OrderInterface $order
    ) {
        $transactionId   = $pendingCapture->getCaptureId();
        $transaction     = $this->getTransaction($transactionId, $payment, $order);
        $invoice         = $this->getInvoice($transactionId, $order);
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Declined amount of %1 online', $formattedAmount);

        $this->getInvoiceAndSetCancelled($transactionId, $order);
        $this->setOnHold($order);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $order->save();

        $this->closeTransaction($transactionId, $payment, $order);
        $pendingCapture->delete();

        $this->addCaptureDeclinedNotice($order);
    }

    protected function getTransaction($transactionId, PaymentInfoInterface $payment, OrderInterface $order)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            TransactionInterface::TXN_ID, $transactionId
        );

        $searchCriteriaBuilder->addFilter(
            TransactionInterface::ORDER_ID, $order->getId()
        );

        $searchCriteriaBuilder->addFilter(
            TransactionInterface::PAYMENT_ID, $payment->getId()
        );

        $searchCriteria = $searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $transactionList = $this->transactionRepository->getList($searchCriteria);

        if (count($items = $transactionList->getItems())) {
            $transaction = current($items);
            $transaction
                ->setPayment($payment)
                ->setOrder($order);

            return $transaction;
        }

        throw new NoSuchEntityException();
    }

    protected function getInvoice($transactionId, OrderInterface $order)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            InvoiceInterface::TRANSACTION_ID, $transactionId
        );

        $searchCriteriaBuilder->addFilter(
            InvoiceInterface::ORDER_ID, $order->getId()
        );

        $searchCriteria = $searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $invoiceList = $this->invoiceRepository->getList($searchCriteria);

        if (count($items = $invoiceList->getItems())) {
            $invoice = current($items);
            $invoice->setOrder($order);
            return $invoice;
        }

        throw new NoSuchEntityException();
    }

    protected function getInvoiceAndSetPaid($transactionId, OrderInterface $order)
    {
        $invoice = $this->getInvoice($transactionId, $order);
        $invoice->pay();
        $order->addRelatedObject($invoice);

        return $invoice;
    }

    protected function getInvoiceAndSetCancelled($transactionId, OrderInterface $order)
    {
        $invoice = $this->getInvoice($transactionId, $order);
        $invoice->cancel();
        $order->addRelatedObject($invoice);

        return $invoice;
    }

    protected function setOnHold(OrderInterface $order)
    {
        $this->setOrderState($order, Order::STATE_HOLDED);
    }

    protected function setProcessing(OrderInterface $order)
    {
        $this->setOrderState($order, Order::STATE_PROCESSING);
    }

    protected function setPaymentReview(OrderInterface $order)
    {
        $this->setOrderState($order, Order::STATE_PAYMENT_REVIEW);
    }

    protected function setOrderState(OrderInterface $order, $state)
    {
        $status = $order->getConfig()->getStateDefaultStatus($state);
        $order->setState($state)->setStatus($status);
    }

    protected function addCaptureDeclinedNotice(OrderInterface $order)
    {
        $orderUrl = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()]);

        $this->notifier->addNotice(
            __('Capture declined'),
            __('Capture declined for Order <a href="%2">#%1</a>', $order->getIncrementId(), $orderUrl),
            $orderUrl
        );
    }
}