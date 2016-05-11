<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Basket as BasketFixture;
use Fixtures\Customer as CustomerFixture;
use PHPUnit_Framework_Assert;

class BasketContext implements SnippetAcceptingContext
{
    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    /**
     * @var BasketFixture
     */
    protected $basketFixture;

    public function __construct()
    {
        $this->customerFixture = new CustomerFixture;
        $this->basketFixture   = new BasketFixture;
    }

    /**
     * @Then the basket for :email should not be linked to an amazon order
     */
    public function theBasketForShouldNotBeLinkedToAnAmazonOrder($email)
    {
        $customer = $this->customerFixture->get($email);
        $basket   = $this->basketFixture->getActiveForCustomer($customer->getId());

        PHPUnit_Framework_Assert::assertNull($basket->getExtensionAttributes()->getAmazonOrderReferenceId());
    }
}