<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Checkout;
use PHPUnit_Framework_Assert;

class ShippingContext implements SnippetAcceptingContext
{
    /**
     * @var Checkout
     */
    protected $checkoutPage;

    public function __construct(Checkout $checkoutPage)
    {
        $this->checkoutPage = $checkoutPage;
    }
    
    /**
     * @Given I select a shipping address from my amazon account
     */
    public function iSelectAShippingAddressFromMyAmazonAccount()
    {
        $this->checkoutPage->selectFirstAmazonShippingAddress();
    }

    /**
     * @Given I select a valid shipping method
     */
    public function iSelectAValidShippingMethod()
    {
        $this->checkoutPage->selectDefaultShippingMethod();
    }

    /**
     * @Then the standard shipping form should be displayed
     */
    public function theStandardShippingFormShouldBeDisplayed()
    {
        $hasShippingForm = $this->checkoutPage->hasStandardShippingForm();
        PHPUnit_Framework_Assert::assertTrue($hasShippingForm);
    }
}