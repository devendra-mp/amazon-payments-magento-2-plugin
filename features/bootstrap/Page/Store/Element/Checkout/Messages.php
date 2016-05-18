<?php

namespace Page\Store\Element\Checkout;

use PHPUnit_Framework_Assert;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class Messages extends Element
{
    protected $selector = '.messages';

    public function hasHardDeclineError()
    {
        try {
            $element    = $this->find('css', '.message-error div');
            $constraint = PHPUnit_Framework_Assert::stringContains(
                'Unfortunately it is not possible to pay with Amazon for this order, Please choose another payment method.',
                false
            );

            PHPUnit_Framework_Assert::assertThat($element->getText(), $constraint);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}