<?php

namespace Amazon\Payment\Plugin;

use Amazon\Payment\Model\Method\Amazon;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Controller\Adminhtml\Order\Invoice\Save;
use Magento\Sales\Model\Order\Invoice;

class InvoiceSave
{
    /**
     * @var OrderInterfaceFactory
     */
    protected $orderFactory;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(OrderInterfaceFactory $orderFactory, Context $context)
    {
        $this->orderFactory = $orderFactory;
        $this->context      = $context;
    }

    public function afterExecute(Save $save, Redirect $redirect)
    {
        $orderId = $save->getRequest()->getParam('order_id');
        $order   = $this->orderFactory->create();
        $order->load($orderId);

        if ($order->getPayment() && Amazon::PAYMENT_METHOD_CODE == $order->getPayment()->getMethod()) {
            $lastInvoice = $order->getInvoiceCollection()->getLastItem();

            if ($lastInvoice && Invoice::STATE_OPEN == $lastInvoice->getState()) {
                $this->context->getMessageManager()->addError(__('Capture pending approval from the payment gateway'));
            }
        }

        return $redirect;
    }
}