<?php

namespace Amazon\Payment\Observer;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Payment\Api\Data\QuoteLinkInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Model\OrderInformationManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class ConfirmOrder implements ObserverInterface
{
    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    /**
     * @var OrderInformationManagement
     */
    protected $orderInformationManagement;

    /**
     * ConfirmOrder constructor.
     *
     * @param QuoteLinkInterfaceFactory  $quoteLinkFactory
     * @param OrderInformationManagement $orderInformationManagement
     */
    public function __construct(
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        OrderInformationManagement $orderInformationManagement
    ) {
        $this->quoteLinkFactory           = $quoteLinkFactory;
        $this->orderInformationManagement = $orderInformationManagement;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();

        if ($order instanceof Order) {
            $quoteId   = $order->getQuoteId();
            $quoteLink = $this->quoteLinkFactory->create();
            $quoteLink->load($quoteId, 'quote_id');
            $amazonOrderReferenceId = $quoteLink->getAmazonOrderReferenceId();

            if ($amazonOrderReferenceId) {
                $this->saveOrderInformation($quoteLink, $amazonOrderReferenceId);
                $this->confirmOrderReference($quoteLink, $amazonOrderReferenceId);
            }
        }
    }

    protected function saveOrderInformation(QuoteLinkInterface $quoteLink, $amazonOrderReferenceId)
    {
        if ( ! $quoteLink->isConfirmed()) {
            $saveOrderInformation = $this->orderInformationManagement->saveOrderInformation($amazonOrderReferenceId);

            if ( ! $saveOrderInformation) {
                throw new AmazonServiceUnavailableException();
            }
        }
    }

    protected function confirmOrderReference(QuoteLinkInterface $quoteLink, $amazonOrderReferenceId)
    {
        $confirmOrderReference = $this->orderInformationManagement->confirmOrderReference($amazonOrderReferenceId);

        if ( ! $confirmOrderReference) {
            throw new AmazonServiceUnavailableException();
        }

        $quoteLink->setConfirmed(true)->save();
    }
}
