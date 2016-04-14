<?php

namespace Amazon\Payment\Observer;

use Amazon\Payment\Api\Data\OrderLinkInterfaceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;

class LoadOrder implements ObserverInterface
{
    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * @var OrderLinkInterfaceFactory
     */
    protected $orderLinkFactory;

    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        OrderLinkInterfaceFactory $orderLinkFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderLinkFactory      = $orderLinkFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $this->setAmazonOrderReferenceIdExtensionAttribute($order);
    }

    protected function setAmazonOrderReferenceIdExtensionAttribute(OrderInterface $order)
    {
        $orderExtension = ($order->getExtensionAttributes()) ?: $this->orderExtensionFactory->create();

        if ($order->getId()) {
            $amazonOrder = $this->orderLinkFactory->create();
            $amazonOrder->load($order->getId(), 'order_id');

            if ($amazonOrder->getId()) {
                $orderExtension->setAmazonOrderReferenceId($amazonOrder->getAmazonOrderReferenceId());
            }
        }

        $order->setExtensionAttributes($orderExtension);
    }
}