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
use Amazon\Payment\Api\Data\PendingAuthorizationInterface;
use Amazon\Payment\Api\Data\PendingAuthorizationInterfaceFactory;
use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\Data\PendingCaptureInterfaceFactory;
use Amazon\Payment\Api\Data\PendingRefundInterfaceFactory;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonAuthorizationDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonAuthorizationResponse;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponse;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonCaptureResponse;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Amazon\Payment\Domain\Validator\AmazonAuthorization;
use Amazon\Payment\Exception\SoftDeclineException;
use Amazon\Payment\Domain\AmazonRefundResponse;
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
     */
    public function __construct(
        PendingCaptureInterfaceFactory            $pendingCaptureFactory,
        PendingAuthorizationInterfaceFactory      $pendingAuthorizationFactory,
        ClientFactoryInterface                    $clientFactory,
        AmazonCaptureDetailsResponseFactory       $amazonCaptureDetailsResponseFactory,
        AmazonAuthorizationDetailsResponseFactory $amazonAuthorizationDetailsResponseFactory,
        AmazonAuthorization                       $amazonAuthorizationValidator,
        NotifierInterface                         $notifier,
        UrlInterface                              $urlBuilder,
        SearchCriteriaBuilderFactory              $searchCriteriaBuilderFactory,
        OrderPaymentRepositoryInterface           $orderPaymentRepository,
        OrderRepositoryInterface                  $orderRepository,
        TransactionRepositoryInterface            $transactionRepository,
        InvoiceRepositoryInterface                $invoiceRepository,
        PendingRefundInterfaceFactory             $pendingRefundFactory,
        ManagerInterface                          $eventManager
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
    }

    /**
     * {@inheritdoc}
     */
    public function updateAuthorization($pendingAuthorizationId)
    {
        try {
            $pendingAuthorization = $this->pendingAuthorizationFactory->create();
            $pendingAuthorization->getResource()->beginTransaction();
            $pendingAuthorization->setLockOnLoad(true);
            $pendingAuthorization->load($pendingAuthorizationId);

            if ($pendingAuthorization->getOrderId()) {
                if ($pendingAuthorization->getAuthorizationId()) {
                    $this->processUpdateAuthorization($pendingAuthorization);
                } else {
                    //check for order state change and open new authorization
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
    public function updateCapture($pendingCaptureId)
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

                $responseParser = $this->clientFactory->create($order->getStoreId())->getCaptureDetails([
                    'amazon_capture_id' => $pendingCapture->getCaptureId()
                ]);

                $response = $this->amazonCaptureDetailsResponseFactory->create(['response' => $responseParser]);
                $this->processUpdateCaptureResponse($response, $pendingCapture, $payment, $order);
            }

            $pendingCapture->getResource()->commit();
        } catch (Exception $e) {
            $pendingCapture->getResource()->rollBack();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingCapture(AmazonCaptureResponse $response, $paymentId, $orderId)
    {
        $this->pendingCaptureFactory->create()
            ->setCaptureId($response->getTransactionId())
            ->setPaymentId($paymentId)
            ->setOrderId($orderId)
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingAuthorization(AmazonAuthorizationResponse $response, OrderInterface $order)
    {
        $pendingAuthorization = $this->pendingAuthorizationFactory->create()
            ->setAuthorizationId($response->getAuthorizeTransactionId());

        $order->addRelatedObject($pendingAuthorization);
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingRefund(AmazonRefundResponse $response, PaymentInfoInterface $payment)
    {
        $this->pendingRefundFactory->create()
            ->setRefundId($response->getRefundId())
            ->setPaymentId($payment->getId())
            ->setOrderId($payment->getOrder()->getId())
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function closeTransaction($transactionId, $paymentId, $orderId)
    {
        $this->getTransaction($transactionId, $paymentId, $orderId)->setIsClosed(1)->save();
    }

    protected function processUpdateAuthorization(PendingAuthorizationInterface $pendingAuthorization)
    {
        $order   = $this->orderRepository->get($pendingAuthorization->getOrderId());
        $payment = $this->orderPaymentRepository->get($pendingAuthorization->getPaymentId());
        $order->setPayment($payment);
        $authorizationId = $pendingAuthorization->getAuthorizationId();

        $responseParser = $this->clientFactory->create($order->getStoreId())->getAuthorizationDetails([
            'amazon_authorization_id' => $authorizationId
        ]);

        $response = $this->amazonAuthorizationDetailsResponseFactory->create([
            'response' => $responseParser
        ]);

        $capture = $response->hasCapture();

        try {
            $this->amazonAuthorizationValidator->validate($response);

            if ( ! $response->isPending()) {
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
        $capture
    ) {
        $authorizationId = $pendingAuthorization->getAuthorizationId();

        $this->setProcessing($order);

        if ($capture) {
            $invoice = $this->getInvoiceAndSetPaid($authorizationId, $order);
            $this->closeTransaction($authorizationId, $payment->getId(), $order->getId());
            $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
            $message         = __('Captured amount of %1 online', $formattedAmount);
        } else {
            $formattedAmount = $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized());
            $message         = __('Authorized amount of %1 online', $formattedAmount);
        }

        $transaction = $this->getTransaction($authorizationId, $payment->getId(), $order->getId());
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
            $invoice = $this->getInvoiceAndSetCancelled($authorizationId, $order);
            $payment->cancelInvoice($invoice);
            $this->setPaymentReview($order);
            $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        } else {
            $formattedAmount = $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        }

        $transaction = $this->getTransaction($authorizationId, $payment->getId(), $order->getId());
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $payment->setAmountAuthorized(null);
        $payment->setBaseAmountAuthorized(null);

        $this->closeTransaction($authorizationId, $payment->getId(), $order->getId());
        $pendingAuthorization->setAuthorizationId(null);
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
            $invoice = $this->getInvoiceAndSetCancelled($authorizationId, $order);
            $payment->cancelInvoice($invoice);
            $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        } else {
            $formattedAmount = $order->getBaseCurrency()->formatTxt($payment->getBaseAmountAuthorized());
            $message         = __('Declined amount of %1 online', $formattedAmount);
        }

        $transaction = $this->getTransaction($authorizationId, $payment->getId(), $order->getId());
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $payment->setAmountAuthorized(null);
        $payment->setBaseAmountAuthorized(null);

        $this->closeTransaction($authorizationId, $payment->getId(), $order->getId());
        $this->setOnHold($order);
        $pendingAuthorization->delete();
        $order->save();
    }

    protected function processUpdateCaptureResponse(
        AmazonCaptureDetailsResponse $response,
        PendingCaptureInterface $pendingCapture,
        OrderPaymentInterface $payment,
        OrderInterface $order
    ) {
        $status = $response->getStatus();

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
        $transaction     = $this->getTransaction($transactionId, $payment->getId(), $order->getId());
        $invoice         = $this->getInvoice($transactionId, $order);
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Captured amount of %1 online', $formattedAmount);

        $this->getInvoiceAndSetPaid($transactionId, $order);
        $payment->setDataUsingMethod('base_amount_paid_online', $invoice->getBaseGrandTotal());
        $this->setProcessing($order);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $order->save();

        $this->closeTransaction($transactionId, $payment->getId(), $order->getId());
        $pendingCapture->delete();
    }

    protected function declinePendingCapture(
        PendingCaptureInterface $pendingCapture,
        OrderPaymentInterface $payment,
        OrderInterface $order
    ) {
        $transactionId   = $pendingCapture->getCaptureId();
        $transaction     = $this->getTransaction($transactionId, $payment->getId(), $order->getId());
        $invoice         = $this->getInvoice($transactionId, $order);
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Declined amount of %1 online', $formattedAmount);

        $this->getInvoiceAndSetCancelled($transactionId, $order);
        $payment->cancelInvoice($invoice);
        $this->setOnHold($order);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $order->save();

        $this->closeTransaction($transactionId, $payment->getId(), $order->getId());
        $pendingCapture->delete();

        $orderUrl = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()]);

        $this->notifier->addNotice(
            __('Capture declined'),
            __('Capture declined for Order <a href="%2">#%1</a>', $order->getIncrementId(), $orderUrl),
            $orderUrl
        );
    }

    protected function getTransaction($transactionId, $paymentId, $orderId)
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $searchCriteriaBuilder->addFilter(
            TransactionInterface::TXN_ID, $transactionId
        );

        $searchCriteriaBuilder->addFilter(
            TransactionInterface::ORDER_ID, $orderId
        );

        $searchCriteriaBuilder->addFilter(
            TransactionInterface::PAYMENT_ID, $paymentId
        );

        $searchCriteria = $searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $transactionList = $this->transactionRepository->getList($searchCriteria);

        if (count($items = $transactionList->getItems())) {
            return current($items);
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
}
