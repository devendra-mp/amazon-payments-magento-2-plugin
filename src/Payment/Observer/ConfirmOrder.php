<?php

namespace Amazon\Payment\Observer;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Payment\Api\Data\QuoteLinkInterface;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Model\Method\Amazon;
use Amazon\Payment\Model\OrderInformationManagement;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
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
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * ConfirmOrder constructor.
     *
     * @param QuoteLinkInterfaceFactory  $quoteLinkFactory
     * @param OrderInformationManagement $orderInformationManagement
     */
    public function __construct(
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        OrderInformationManagement $orderInformationManagement,
        PaymentMethodManagementInterface $paymentMethodManagement
    ) {
        $this->quoteLinkFactory           = $quoteLinkFactory;
        $this->orderInformationManagement = $orderInformationManagement;
        $this->paymentMethodManagement    = $paymentMethodManagement;
    }

    public function execute(Observer $observer)
    {
        $order                  = $observer->getOrder();
        $quoteId                = $order->getQuoteId();
        $quoteLink              = $this->getQuoteLink($quoteId);
        $amazonOrderReferenceId = $quoteLink->getAmazonOrderReferenceId();

        if ($amazonOrderReferenceId) {
            $payment = $this->paymentMethodManagement->get($quoteId);
            if (Amazon::PAYMENT_METHOD_CODE == $payment->getMethod()) {
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

    protected function getQuoteLink($quoteId)
    {
        $quoteLink = $this->quoteLinkFactory->create();
        $quoteLink->load($quoteId, 'quote_id');

        return $quoteLink;
    }
}
