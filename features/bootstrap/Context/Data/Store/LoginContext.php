<?php

namespace Context\Data\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Magento\Customer\Api\Data\CustomerInterface;
use PHPUnit_Framework_Assert;

class LoginContext implements SnippetAcceptingContext
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
     * @Given :email has never logged in with amazon
     */
    public function hasNeverLoggedInWithAmazon($email)
    {
        $customer = $this->customerFixture->get($email);
        PHPUnit_Framework_Assert::assertEquals($email, $customer->getEmail());
    }
}