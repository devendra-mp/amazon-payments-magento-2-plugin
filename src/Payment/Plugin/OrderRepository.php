<?php

namespace Amazon\Payment\Plugin;

use Amazon\Payment\Api\Data\OrderLinkInterfaceFactory;
use Magento\Sales\Api\Data\OrderExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderRepository
{
    /**
     * @var OrderExtensionInterfaceFactory
     */
    protected $orderExtensionFactory;

    /**
     * @var OrderLinkInterfaceFactory
     */
    protected $orderLinkFactory;

    public function __construct(
        OrderExtensionInterfaceFactory $orderExtensionFactory,
        OrderLinkInterfaceFactory $orderLinkFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderLinkFactory      = $orderLinkFactory;
    }

    public function afterGet(OrderRepositoryInterface $orderRepository, OrderInterface $order)
    {
        $this->setAmazonOrderReferenceIdExtensionAttribute($order);

        return $order;
    }

    protected function setAmazonOrderReferenceIdExtensionAttribute(OrderInterface $order)
    {
        $orderExtension = ($order->getExtensionAttributes()) ?: $this->orderExtensionFactory->create();

        $amazonOrder = $this->orderLinkFactory->create();
        $amazonOrder->load($order->getId(), 'order_id');

        if ($amazonOrder->getId()) {
            $orderExtension->setAmazonOrderReferenceId($amazonOrder->getAmazonOrderReferenceId());
        }

        $amazonOrder->setExtensionAttributes($orderExtension);
    }
}