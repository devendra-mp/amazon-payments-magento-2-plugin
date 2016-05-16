<?php

namespace Amazon\Payment\Observer;

use Amazon\Payment\Api\Data\OrderLinkInterfaceFactory;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class CopyOrderReference implements ObserverInterface
{
    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    /**
     * @var OrderLinkInterfaceFactory
     */
    protected $orderLinkFactory;

    public function __construct(
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        OrderLinkInterfaceFactory $orderLinkFactory
    ) {
        $this->quoteLinkFactory = $quoteLinkFactory;
        $this->orderLinkFactory = $orderLinkFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();

        if ($order instanceof Order) {
            $orderId = $order->getId();
            $quoteId = $order->getQuoteId();

            $quoteLink = $this->quoteLinkFactory->create();
            $quoteLink->load($quoteId, 'quote_id');

            $amazonOrderReferenceId = $quoteLink->getAmazonOrderReferenceId();
            if ( ! is_null($amazonOrderReferenceId)) {
                $orderLink = $this->orderLinkFactory->create();
                $orderLink
                    ->load($orderId, 'order_id')
                    ->setAmazonOrderReferenceId($amazonOrderReferenceId)
                    ->setOrderId($orderId)
                    ->save();
            }
        }
    }
}
