<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Checkout;
use Page\Element\Checkout\Messages;
use Page\Element\Checkout\PaymentMethods;
use Page\Element\Checkout\SandboxSimulation;
use PHPUnit_Framework_Assert;

class BillingContext implements SnippetAcceptingContext
{
    /**
     * @var Checkout
     */
    protected $checkoutPage;

    /**
     * @var Messages
     */
    protected $messagesElement;

    /**
     * @var PaymentMethods
     */
    protected $paymentMethodsElement;

    public function __construct(
        Checkout $checkoutPage,
        Messages $messagesElement,
        PaymentMethods $paymentMethodsElement
    ) {
        $this->checkoutPage             = $checkoutPage;
        $this->messagesElement          = $messagesElement;
        $this->paymentMethodsElement    = $paymentMethodsElement;
    }

    /**
     * @Then the amazon payment widget should be displayed
     */
    public function theAmazonPaymentWidgetShouldBeDisplayed()
    {
        $hasWidget = $this->checkoutPage->hasPaymentWidget();
        PHPUnit_Framework_Assert::assertTrue($hasWidget);
    }


    /**
     * @Then the amazon payment widget should not be displayed
     */
    public function theAmazonPaymentWidgetShouldNotBeDisplayed()
    {
        $hasWidget = $this->checkoutPage->hasPaymentWidget();
        PHPUnit_Framework_Assert::assertFalse($hasWidget);
    }

    /**
     * @Given I provide a valid shipping address
     */
    public function iProvideAValidShippingAddress()
    {
        $hasShippingForm = $this->checkoutPage->hasStandardShippingForm();
        PHPUnit_Framework_Assert::assertTrue($hasShippingForm);

        $this->checkoutPage->provideShippingAddress();
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
     * @Given I am requesting authorization on a payment that will be rejected
     */
    public function iAmRequestingAuthorizationOnAPaymentThatWillBeRejected()
    {
        $this->checkoutPage->selectSimulation(SandboxSimulation::SIMULATION_REJECTED);
    }

    /**
     * @Given I am requesting authorization on a payment that will timeout
     */
    public function iAmRequestingAuthorizationOnAPaymentThatWillTimeout()
    {
        $this->checkoutPage->selectSimulation(SandboxSimulation::SIMILATION_TIMEOUT);
    }

    /**
     * @Given I am requesting authorization on a payment that will use an invalid method
     */
    public function iAmRequestingAuthorizationOnAPaymentThatWillUseAnInvalidMethod()
    {
        $this->checkoutPage->selectSimulation(SandboxSimulation::SIMULATION_INVALID_PAYMENT);
    }

    /**
     * @Then I am requesting authorization on a payment that will be valid
     */
    public function iAmRequestingAuthorizationOnAPaymentThatWillBeValid()
    {
        $this->checkoutPage->selectSimulation(SandboxSimulation::NO_SIMULATION);
    }

    /**
     * @Then I should be notified that my payment was rejected
     */
    public function iShouldBeNotifiedThatMyPaymentWasRejected()
    {
        $hardDecline = $this->messagesElement->hasHardDeclineError();
        PHPUnit_Framework_Assert::assertTrue($hardDecline);
    }

    /**
     * @Then I should be notified that my payment was invalid
     */
    public function iShouldBeNotifiedThatMyPaymentWasInvalid()
    {
        $softDecline = $this->messagesElement->hasSoftDeclineError();
        PHPUnit_Framework_Assert::assertTrue($softDecline);
    }

    /**
     * @Then the amazon wallet widget should be removed
     */
    public function theAmazonWalletWidgetShouldBeRemoved()
    {
        $hasWidget = $this->checkoutPage->hasPaymentWidget();
        PHPUnit_Framework_Assert::assertFalse($hasWidget);
    }

    /**
     * @Then I should be able to select a payment method
     * @Then I should be able to select an alternative payment method
     */
    public function iShouldBeAbleToSelectAnAlternativePaymentMethod()
    {
        $hasAlternativeMethods = $this->paymentMethodsElement->hasMethods();
        PHPUnit_Framework_Assert::assertTrue($hasAlternativeMethods);
    }

    /**
     * @Then I should be able to select an alternative payment method from my amazon account
     */
    public function iShouldBeAbleToSelectAnAlternativePaymentMethodFromMyAmazonAccount()
    {
        $this->checkoutPage->selectAlternativeAmazonPaymentMethod();
    }
}