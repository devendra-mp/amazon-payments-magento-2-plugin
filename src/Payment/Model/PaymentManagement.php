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
use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\Data\PendingCaptureInterfaceFactory;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponse;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonCaptureResponse;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Exception;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Notification\NotifierInterface;
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
     * PaymentManagement constructor.
     *
     * @param PendingCaptureInterfaceFactory      $pendingCaptureFactory
     * @param ClientFactoryInterface              $clientFactory
     * @param AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory
     * @param NotifierInterface                   $notifier
     * @param UrlInterface                        $urlBuilder
     * @param SearchCriteriaBuilderFactory        $searchCriteriaBuilderFactory
     * @param OrderPaymentRepositoryInterface     $orderPaymentRepository
     * @param OrderRepositoryInterface            $orderRepository
     * @param TransactionRepositoryInterface      $transactionRepository
     * @param InvoiceRepositoryInterface          $invoiceRepository
     */
    public function __construct(
        PendingCaptureInterfaceFactory $pendingCaptureFactory,
        ClientFactoryInterface $clientFactory,
        AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory,
        NotifierInterface $notifier,
        UrlInterface $urlBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        OrderRepositoryInterface $orderRepository,
        TransactionRepositoryInterface $transactionRepository,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->clientFactory                       = $clientFactory;
        $this->pendingCaptureFactory               = $pendingCaptureFactory;
        $this->amazonCaptureDetailsResponseFactory = $amazonCaptureDetailsResponseFactory;
        $this->notifier                            = $notifier;
        $this->urlBuilder                          = $urlBuilder;
        $this->searchCriteriaBuilderFactory        = $searchCriteriaBuilderFactory;
        $this->orderPaymentRepository              = $orderPaymentRepository;
        $this->orderRepository                     = $orderRepository;
        $this->transactionRepository               = $transactionRepository;
        $this->invoiceRepository                   = $invoiceRepository;
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
    public function closeTransaction($transactionId, $paymentId, $orderId)
    {
        $this->getTransaction($transactionId, $paymentId, $orderId)->setIsClosed(1)->save();
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
        $state           = Order::STATE_PROCESSING;
        $transaction     = $this->getTransaction($transactionId, $payment->getId(), $order->getId());
        $invoice         = $this->getInvoice($transactionId, $order);
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Captured amount of %1 online', $formattedAmount);

        $invoice->pay();
        $payment->setDataUsingMethod('base_amount_paid_online', $invoice->getBaseGrandTotal());

        $order->addRelatedObject($invoice);
        $order->setState($state)->setStatus($order->getConfig()->getStateDefaultStatus($state));
        $order->getPayment()->addTransactionCommentsToOrder($transaction, $message);
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
        $state           = Order::STATE_HOLDED;
        $transaction     = $this->getTransaction($transactionId, $payment->getId(), $order->getId());
        $invoice         = $this->getInvoice($transactionId, $order);
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Declined amount of %1 online', $formattedAmount);

        $invoice->cancel();

        $order->addRelatedObject($invoice);
        $order->setState($state)->setStatus($order->getConfig()->getStateDefaultStatus($state));
        $order->getPayment()->addTransactionCommentsToOrder($transaction, $message);
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
}