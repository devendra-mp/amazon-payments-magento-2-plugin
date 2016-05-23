<?php

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
use Magento\Framework\Notification\NotifierInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\Data\TransactionInterfaceFactory;
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
     * @var TransactionInterfaceFactory
     */
    protected $transactionFactory;

    /**
     * @var InvoiceInterfaceFactory
     */
    protected $invoiceFactory;

    /**
     * @var NotifierInterface
     */
    protected $notifier;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * PaymentManagement constructor.
     *
     * @param PendingCaptureInterfaceFactory      $pendingCaptureFactory
     * @param ClientFactoryInterface              $clientFactory
     * @param AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory
     * @param TransactionInterfaceFactory         $transactionFactory
     * @param InvoiceInterfaceFactory             $invoiceFactory
     * @param NotifierInterface                   $notifier
     * @param UrlInterface                        $urlBuilder
     */
    public function __construct(
        PendingCaptureInterfaceFactory $pendingCaptureFactory,
        ClientFactoryInterface $clientFactory,
        AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory,
        TransactionInterfaceFactory $transactionFactory,
        InvoiceInterfaceFactory $invoiceFactory,
        NotifierInterface $notifier,
        UrlInterface $urlBuilder
    ) {
        $this->clientFactory                       = $clientFactory;
        $this->pendingCaptureFactory               = $pendingCaptureFactory;
        $this->amazonCaptureDetailsResponseFactory = $amazonCaptureDetailsResponseFactory;
        $this->transactionFactory                  = $transactionFactory;
        $this->invoiceFactory                      = $invoiceFactory;
        $this->notifier                            = $notifier;
        $this->urlBuilder                          = $urlBuilder;
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
                $responseParser = $this->clientFactory->create()->getCaptureDetails([
                    'amazon_capture_id' => $pendingCapture->getCaptureId()
                ]);

                $response = $this->amazonCaptureDetailsResponseFactory->create(['response' => $responseParser]);
                $this->processUpdateCaptureResponse($response, $pendingCapture);
            }

            $pendingCapture->getResource()->commit();
        } catch (Exception $e) {
            $pendingCapture->getResource()->rollBack();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function queuePendingCapture(AmazonCaptureResponse $response)
    {
        $this->pendingCaptureFactory->create()
            ->setCaptureId($response->getTransactionId())
            ->save();
    }

    /**
     * {@inheritdoc}
     */
    public function closeTransaction($transactionId)
    {
        $this->getTransaction($transactionId)->setIsClosed(1)->save();
    }

    protected function processUpdateCaptureResponse(
        AmazonCaptureDetailsResponse $response,
        PendingCaptureInterface $pendingCapture
    ) {
        $status = $response->getStatus();

        switch ($status->getState()) {
            case AmazonCaptureStatus::STATE_COMPLETED:
                $this->completePendingCapture($pendingCapture);
                break;
            case AmazonCaptureStatus::STATE_DECLINED:
                $this->declinePendingCapture($pendingCapture);
                break;
        }
    }

    protected function completePendingCapture(PendingCaptureInterface $pendingCapture)
    {
        $transactionId   = $pendingCapture->getCaptureId();
        $state           = Order::STATE_PROCESSING;
        $transaction     = $this->getTransaction($transactionId);
        $invoice         = $this->getInvoice($transactionId);
        $order           = $invoice->getOrder();
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Captured amount of %1 online', $formattedAmount);

        $invoice->pay();

        $order->addRelatedObject($invoice);
        $order->setState($state)->setStatus($order->getConfig()->getStateDefaultStatus($state));
        $order->getPayment()->addTransactionCommentsToOrder($transaction, $message);
        $order->save();

        $this->closeTransaction($transactionId);
        $pendingCapture->delete();
    }

    protected function declinePendingCapture(PendingCaptureInterface $pendingCapture)
    {
        $transactionId   = $pendingCapture->getCaptureId();
        $transaction     = $this->getTransaction($transactionId);
        $invoice         = $this->getInvoice($transactionId);
        $order           = $invoice->getOrder();
        $formattedAmount = $order->getBaseCurrency()->formatTxt($invoice->getBaseGrandTotal());
        $message         = __('Declined amount of %1 online', $formattedAmount);

        $invoice->cancel();

        $order->addRelatedObject($invoice);
        $order->registerCancellation('', false);
        $order->getPayment()->addTransactionCommentsToOrder($transaction, $message);
        $order->save();

        $this->closeTransaction($transactionId);
        $pendingCapture->delete();

        $orderUrl = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()]);

        $this->notifier->addNotice(
            __('Capture declined'),
            __('Capture declined for Order <a href="%2">#%1</a>', $order->getIncrementId(), $orderUrl),
            $orderUrl
        );
    }

    protected function getTransaction($transactionId)
    {
        return $this->transactionFactory->create()
            ->load($transactionId, TransactionInterface::TXN_ID);
    }

    protected function getInvoice($transactionId)
    {
        return $this->invoiceFactory->create()
            ->load($transactionId, InvoiceInterface::TRANSACTION_ID);
    }
}