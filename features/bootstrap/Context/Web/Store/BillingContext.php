<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Checkout;
use Page\Store\Element\SandboxSimulation;
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

    public function __construct(Checkout $checkoutPage, SandboxSimulation $sandboxSimulationElement)
    {
        $this->checkoutPage             = $checkoutPage;
        $this->sandboxSimulationElement = $sandboxSimulationElement;
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
        throw new PendingException();
    }

    /**
     * @Then the amazon wallet widget should be removed
     */
    public function theAmazonWalletWidgetShouldBeRemoved()
    {
        throw new PendingException();
    }

    /**
     * @Then I should be able to select an alternative payment method
     */
    public function iShouldBeAbleToSelectAnAlternativePaymentMethod()
    {
        throw new PendingException();
    }
}