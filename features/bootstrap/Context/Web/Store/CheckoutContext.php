<?php
namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Checkout;

class CheckoutContext implements SnippetAcceptingContext
{
    /**
     * @var Checkout
     */
    private $checkoutPage;

    public function __construct(Checkout $checkoutPage)
    {
        $this->checkoutPage = $checkoutPage;
    }
    
    /**
     * @Given I go to the checkout
     */
    public function iGoToTheCheckout()
    {
        $this->checkoutPage->open();
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
     * @Given I go to billing
     */
    public function iGoToBilling()
    {
        $this->checkoutPage->goToBilling();
    }

    /**
     * @Given I select a payment method from my amazon account
     */
    public function iSelectAPaymentMethodFromMyAmazonAccount()
    {
        $this->checkoutPage->selectFirstAmazonPaymentMethod();
    }

    /**
     * @Then the billing address for my payment method should be displayed
     */
    public function theBillingAddressForMyPaymentMethodShouldBeDisplayed()
    {
        echo $this->checkoutPage->getBillingAddress();
    }
}