<?php

namespace Context\Store;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Page\Store\CartPage;
use Page\Store\CheckoutPage;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class CheckoutContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    /**
     * @var CheckoutPage
     */
    private $checkoutPage;

    /**
     * @var CartPage
     */
    private $cartPage;

    /**
     * @param CheckoutPage $checkoutPage
     */
    public function __construct(CheckoutPage $checkoutPage, CartPage $cartPage)
    {
        $this->checkoutPage = $checkoutPage;
        $this->cartPage = $cartPage;
    }

    /**
     * @When I open the checkout page
     */
    public function iOpenTheCheckoutPage()
    {
        $this->checkoutPage->openPage();
    }

    /**
     * @When I fill the checkout form with correct data for the :country
     */
    public function iFillTheCheckoutFormWithCorrectData($country)
    {
        $this->checkoutPage->fillShippingFormWithTestData($country);
    }

    /**
     * @Then I should be able to place an order
     */
    public function iShouldBeAbleToPlaceAnOrder()
    {
        $this->checkoutPage->placeOrder();
    }

    /**
     * @Then I should see the order confirmation page
     */
    public function iShouldSeeTheOrderConfirmationPage()
    {
        if (!$this->checkoutPage->canSeeConfirmationPage()) {
            throw new \Exception("I can't see the order confirmation page");
        }
    }

    /**
     * @Given I see the sample fragrance form on the success page
     */
    public function iSeeTheSampleFragranceFormOnTheSuccessPage()
    {
        if (!$this->checkoutPage->canSeeSampleFragranceForm()) {
            throw new \Exception("I can't see the sample fragrance form");
        }
    }

    /**
     * @When I fill the sample fragrance form with correct data
     */
    public function iFillTheSampleFragranceFormWithCorrectData()
    {
        $this->checkoutPage->fillSampleFragranceFormWithTestData();
    }

    /**
     * @Then I should be able to send the samples
     */
    public function iShouldBeAbleToSendTheSamples()
    {
        $this->checkoutPage->sendSampleFragrances();
    }

    /**
     * @Then I should see the sample fragrance successfully sent message
     */
    public function iShouldSeeTheSampleFragranceSuccessfullySentMessage()
    {
        if (!$this->checkoutPage->canSeeSampleFragranceSuccessfullySentMessage()) {
            throw new \Exception("I can't see the success message");
        }
    }

    /**
     * @When I open the cart page
     */
    public function iOpenTheCartPage()
    {
        $this->cartPage->openPage();
    }

    /**
     * @When I apply the :couponCode coupon code
     */
    public function iApplyTheCouponCode($couponCode)
    {
        $this->cartPage->applyCouponCode($couponCode);
    }
}
