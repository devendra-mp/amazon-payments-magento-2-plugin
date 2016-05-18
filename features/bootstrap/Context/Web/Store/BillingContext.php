<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Checkout;
use Page\Store\Element\Checkout\Messages;
use Page\Store\Element\Checkout\PaymentMethods;
use Page\Store\Element\Checkout\SandboxSimulation;
use PHPUnit_Framework_Assert;

class BillingContext implements SnippetAcceptingContext
{
    /**
     * @var Checkout
     */
    protected $checkoutPage;

    /**
     * @var SandboxSimulation
     */
    protected $sandboxSimulationElement;

    /**
     * @var Messages
     */
    protected $messagesElement;
    /**
     * @var PaymentMethods
     */
    private $paymentMethodsElement;

    public function __construct(
        Checkout $checkoutPage,
        SandboxSimulation $sandboxSimulationElement,
        Messages $messagesElement,
        PaymentMethods $paymentMethodsElement
    ) {
        $this->checkoutPage             = $checkoutPage;
        $this->sandboxSimulationElement = $sandboxSimulationElement;
        $this->messagesElement          = $messagesElement;
        $this->paymentMethodsElement    = $paymentMethodsElement;
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
        $this->sandboxSimulationElement->selectSimulation(SandboxSimulation::SIMULATION_REJECTED);
    }

    /**
     * @Given I am requesting authorization on a payment that will timeout
     */
    public function iAmRequestingAuthorizationOnAPaymentThatWillTimeout()
    {
        $this->sandboxSimulationElement->selectSimulation(SandboxSimulation::SIMILATION_TIMEOUT);
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
     * @Then the amazon wallet widget should be removed
     */
    public function theAmazonWalletWidgetShouldBeRemoved()
    {
        $hasWidget = $this->checkoutPage->hasPaymentWidget();
        PHPUnit_Framework_Assert::assertFalse($hasWidget);
    }

    /**
     * @Then I should be able to select an alternative payment method
     */
    public function iShouldBeAbleToSelectAnAlternativePaymentMethod()
    {
        $hasAlternativeMethods = $this->paymentMethodsElement->hasMethods();
        PHPUnit_Framework_Assert::assertTrue($hasAlternativeMethods);
    }
}