<?php

namespace Page\Element\Checkout;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class PaymentMethods extends Element
{
    protected $selector = '.payment-methods';
    
    public function hasMethods()
    {
        try {
            return (null !== $this->find('css', 'input[name="payment[method]"]'));
        } catch (\Exception $e) {
            return false;
        }
    }
}