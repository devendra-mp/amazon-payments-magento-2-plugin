<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit_Framework_Assert;

class CustomerContext implements SnippetAcceptingContext
{
    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    public function __construct()
    {
        $this->customerFixture = new CustomerFixture;
    }

    /**
     * @Given there is a customer :email
     */
    public function thereIsACustomer($email)
    {
        $this->customerFixture->create([CustomerInterface::EMAIL => $email]);
    }

    /**
     * @Given there is a not a customer :email
     */
    public function thereIsNotACustomer($email)
    {
        try {
            $customer = $this->customerFixture->get($email, true);
            throw new \Exception('expected to find no customer for ' . $email . ' but one was found');
        } catch (NoSuchEntityException $e) {
            //expected behaviour
        }
    }

    /**
     * @Then a customer :email should have been created
     */
    public function aCustomerShouldHaveBeenCreated($email)
    {
        $customer = $this->customerFixture->get($email, true);
        PHPUnit_Framework_Assert::assertNotNull($customer->getId());
    }

    /**
     * @Given :email has never logged in with amazon
     */
    public function hasNeverLoggedInWithAmazon($email)
    {
        $customer = $this->customerFixture->get($email, true);
        PHPUnit_Framework_Assert::assertNull($customer->getExtensionAttributes()->getAmazonId());
    }

    /**
     * @Then :email is associated with an amazon account
     */
    public function isAssociatedWithAnAmazonAccount($email)
    {
        $customer = $this->customerFixture->get($email, true);
        PHPUnit_Framework_Assert::assertStringStartsWith('amzn1.account', $customer->getExtensionAttributes()->getAmazonId());
    }
}