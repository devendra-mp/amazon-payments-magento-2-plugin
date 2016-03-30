<?php

namespace Amazon\Payment\Model\Observer;

use Amazon\Payment\Model\OrderInformationManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Api\Data\OrderLinkInterfaceFactory;
use Psr\Log\LoggerInterface;

class ConfirmOrder implements ObserverInterface
{
    protected $logger;

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
     * @param LoggerInterface $logger
     * @param QuoteLinkInterfaceFactory $quoteLinkFactory
     * @param OrderLinkInterfaceFactory $orderLinkFactory
     * @param OrderInformationManagement $informationManagement
     */
    public function __construct(LoggerInterface $logger,
                                QuoteLinkInterfaceFactory $quoteLinkFactory,
                                OrderLinkInterfaceFactory $orderLinkFactory,
                                \Amazon\Payment\Model\OrderInformationManagement $informationManagement
    )
    {
        $this->logger = $logger;
        $this->quoteLinkFactory = $quoteLinkFactory;
        $this->orderLinkFactory = $orderLinkFactory;
        $this->orderInformationManager = $informationManagement;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();

        $quoteId = $order->getQuoteId();
        $quoteLink = $this->quoteLinkFactory->create();
        $quoteLink->load($quoteId, 'quote_id');
        $amazonOrderReferenceId = $quoteLink->getAmazonOrderReferenceId();

        $this->orderInformationManager->confirmOrderReference($amazonOrderReferenceId);
    }
}
