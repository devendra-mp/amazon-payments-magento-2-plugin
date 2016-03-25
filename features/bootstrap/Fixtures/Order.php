<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Sales\Model\Order as OrderModel;
use Magento\Sales\Model\Order\Invoice as InvoiceModel;

class Order extends BaseFixture
{
    /**
     * @param  string $incrementId
     *
     * @return OrderModel
     */
    public function getOrderByIncrementId($incrementId)
    {
        $orderModel = $this->createMagentoObject(OrderModel::class);
        $order = $orderModel->loadByAttribute('increment_id', $incrementId);

        if (!$order || is_null($order->getId())) {
            throw new \LogicException("Order {$incrementId} could not be loaded or found");
        }

        $order->load($order->getId());

        return $order;
    }

    /**
     * @param  string $incrementId
     *
     * @return void
     */
    public function invoiceOrder($incrementId)
    {
        $order = $this->getOrderByIncrementId($incrementId);

        if (!$order->canInvoice()) {
            throw new \LogicException("Order {$incrementId} can't be invoiced");
        }

        $order->getPayment()->capture();

        $invoice = $order->prepareInvoice();
        $invoice->setRequestedCaptureCase(InvoiceModel::CAPTURE_OFFLINE);
        $invoice->register();
        $invoice->getOrder()->setIsInProcess(true);

        $transaction = $this->createMagentoObject('Magento\Framework\DB\Transaction');
        $transaction->addObject($invoice);
        $transaction->addObject($invoice->getOrder());
        $transaction->save();
    }

    /**
     * @param string $incrementId
     * @param string $orderStatus
     */
    public function setOrderStatus($incrementId, $orderStatus)
    {
        $order = $this->getOrderByIncrementId($incrementId);
        $order->setStatus($orderStatus);
        $order->save();
    }
}
