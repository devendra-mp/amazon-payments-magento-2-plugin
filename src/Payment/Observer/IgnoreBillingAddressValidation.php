<?php

namespace Amazon\Payment\Observer;

use Amazon\Payment\Model\Method\Amazon;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class IgnoreBillingAddressValidation implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (Amazon::PAYMENT_METHOD_CODE === $quote->getPayment()->getMethod()) {
            $quote->getBillingAddress()->setShouldIgnoreValidation(true);
        }
    }
}