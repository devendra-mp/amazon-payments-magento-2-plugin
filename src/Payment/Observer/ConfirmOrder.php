<?php

namespace Amazon\Payment\Observer;

use Amazon\Payment\Model\OrderInformationManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Api\Data\OrderLinkInterfaceFactory;

class ConfirmOrder implements ObserverInterface
{
    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    /**
     * @var OrderLinkInterfaceFactory
     */
    protected $orderLinkFactory;

    /**
     * @var OrderInformationManagement
     */
    protected $orderInformationManagement;

    /**
     * ConfirmOrder constructor.
     * @param QuoteLinkInterfaceFactory $quoteLinkFactory
     * @param OrderLinkInterfaceFactory $orderLinkFactory
     * @param OrderInformationManagement $orderInformationManagement
     */
    public function __construct(QuoteLinkInterfaceFactory $quoteLinkFactory,
                                OrderLinkInterfaceFactory $orderLinkFactory,
                                OrderInformationManagement $orderInformationManagement
    )
    {
        $this->quoteLinkFactory = $quoteLinkFactory;
        $this->orderLinkFactory = $orderLinkFactory;
        $this->orderInformationManagement = $orderInformationManagement;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();

        if ($order instanceof Order) {
            $quoteId = $order->getQuoteId();
            $quoteLink = $this->quoteLinkFactory->create();
            $quoteLink->load($quoteId, 'quote_id');
            $amazonOrderReferenceId = $quoteLink->getAmazonOrderReferenceId();

            if ($amazonOrderReferenceId) {
                $this->orderInformationManagement->saveOrderInformation($amazonOrderReferenceId);
                $this->orderInformationManagement->confirmOrderReference($amazonOrderReferenceId);
            }
        }
    }
}
