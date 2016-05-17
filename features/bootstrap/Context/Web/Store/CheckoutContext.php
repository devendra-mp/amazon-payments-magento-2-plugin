<?php
namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Checkout;
use PHPUnit_Framework_Assert;

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
        $billingAddress = $this->checkoutPage->getBillingAddress();
        $constraint     = PHPUnit_Framework_Assert::stringContains(
            'Amber Kelly 87 Terrick Rd EILEAN DARACH, IV23 2TW United Kingdom',
            false
        );

        PHPUnit_Framework_Assert::assertThat($billingAddress, $constraint);
    }

    /**
     * @When I revert to standard checkout
     */
    public function iRevertToStandardCheckout()
    {
        $this->checkoutPage->revertToStandardCheckout();
    }

    /**
     * @Then the standard shipping form should be displayed
     */
    public function theStandardShippingFormShouldBeDisplayed()
    {
        $hasShippingForm = $this->checkoutPage->hasStandardShippingForm();
        PHPUnit_Framework_Assert::assertTrue($hasShippingForm);
    }

    /**
     * @Then I do not see a pay with amazon button
     */
    public function iDoNotSeeAPayWithAmazonButton()
    {
        $hasPwa = $this->checkoutPage->hasPayWithAmazonButton();
        PHPUnit_Framework_Assert::assertFalse($hasPwa);
    }
}