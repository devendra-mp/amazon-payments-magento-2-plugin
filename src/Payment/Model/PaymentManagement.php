<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Helper\Data as CoreHelper;
use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\Data\PendingCaptureInterfaceFactory;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponse;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonCaptureResponse;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Exception;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceInterfaceFactory;
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
     * @var CoreHelper
     */
    protected $coreHelper;

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

    public function __construct(
        PendingCaptureInterfaceFactory $pendingCaptureFactory,
        ClientFactoryInterface $clientFactory,
        CoreHelper $coreHelper,
        AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory,
        TransactionInterfaceFactory $transactionFactory,
        InvoiceInterfaceFactory $invoiceFactory
    ) {
        $this->clientFactory                       = $clientFactory;
        $this->pendingCaptureFactory               = $pendingCaptureFactory;
        $this->coreHelper                          = $coreHelper;
        $this->amazonCaptureDetailsResponseFactory = $amazonCaptureDetailsResponseFactory;
        $this->transactionFactory                  = $transactionFactory;
        $this->invoiceFactory                      = $invoiceFactory;
    }

    /**
     * {@inheritDoc}
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
                    'merchant_id'       => $this->coreHelper->getMerchantId(),
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

    public function queuePendingCapture(AmazonCaptureResponse $response)
    {
        $this->pendingCaptureFactory->create()
            ->setCaptureId($response->getTransactionId())
            ->save();
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
        $invoice = $this->getInvoice($pendingCapture->getCaptureId())->pay();
        $state = Order::STATE_PROCESSING;
        $order = $invoice->getOrder();
        $order->setState($state)->setStatus($order->getConfig()->getStateDefaultStatus($state));
        $this->applyPendingCaptureUpdate($invoice, $pendingCapture);
    }

    protected function declinePendingCapture(PendingCaptureInterface $pendingCapture)
    {
        $invoice = $this->getInvoice($pendingCapture->getCaptureId())->cancel();
        $this->applyPendingCaptureUpdate($invoice, $pendingCapture);
    }

    protected function applyPendingCaptureUpdate($invoice, $pendingCapture)
    {
        $this->getTransaction($pendingCapture->getCaptureId())->setIsClosed(1)->save();
        $invoice->save();
        $invoice->getOrder()->save();
        $pendingCapture->delete();
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