<?php

namespace Amazon\Payment\Observer;

use Amazon\Payment\Model\OrderInformationManagement;
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
        if (Order::STATE_COMPLETE == $observer->getTransport()->getState()) {
            /**
             * @var OrderInterface $order
             */
            $order                  = $observer->getOrder();
            $amazonOrderReferenceId = $order->getExtensionAttributes()->getAmazonOrderReferenceId();

            if ($amazonOrderReferenceId) {
                $this->orderInformationManagement->closeOrderReference($amazonOrderReferenceId);
            }
        }
    }
}