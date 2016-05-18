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
    protected $checkoutPage;

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
     * @Given I go to billing
     */
    public function iGoToBilling()
    {
        $this->checkoutPage->goToBilling();
    }
    
    /**
     * @When I revert to standard checkout
     */
    public function iRevertToStandardCheckout()
    {
        $this->checkoutPage->revertToStandardCheckout();
    }

    /**
     * @Then I do not see a pay with amazon button
     */
    public function iDoNotSeeAPayWithAmazonButton()
    {
        $hasPwa = $this->checkoutPage->hasPayWithAmazonButton();
        PHPUnit_Framework_Assert::assertFalse($hasPwa);
    }

    /**
     * @When I place my order
     */
    public function iPlaceMyOrder()
    {
        $this->checkoutPage->submitOrder();
    }

    /**
     * @Then I should be logged out of amazon
     */
    public function iShouldBeLoggedOutOfAmazon()
    {
        $loggedIn = $this->checkoutPage->isLoggedIn();
        PHPUnit_Framework_Assert::assertFalse($loggedIn);
    }
}