<?php

namespace Amazon\Payment\Observer;

use Amazon\Payment\Model\Method\Amazon;
use Amazon\Payment\Model\OrderInformationManagement;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class CompleteOrder implements ObserverInterface
{
    /**
     * @var OrderInformationManagement
     */
    protected $orderInformationManagement;

    public function __construct(
        OrderInformationManagement $orderInformationManagement
    ) {
        $this->orderInformationManagement = $orderInformationManagement;
    }

    public function execute(Observer $observer)
    {
        /**
         * @var OrderInterface $order
         */
        $order    = $observer->getOrder();
        $complete = Order::STATE_COMPLETE;

        if ($order->getState() == $complete && $order->getStoredData()[OrderInterface::STATE] != $complete) {
            $amazonOrderReferenceId = $order->getExtensionAttributes()->getAmazonOrderReferenceId();

            if ($amazonOrderReferenceId && Amazon::PAYMENT_METHOD_CODE == $order->getPayment()->getMethod()) {
                $this->closeOrderReference($amazonOrderReferenceId);
            }
        }
    }

    protected function closeOrderReference($amazonOrderReferenceId)
    {
        try {
            $this->orderInformationManagement->closeOrderReference($amazonOrderReferenceId);
        } catch (Exception $e) {
        }
    }
}