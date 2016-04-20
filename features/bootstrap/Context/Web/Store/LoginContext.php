<?php

namespace Context\Web\Store;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Exception;
use Fixtures\Customer as CustomerFixture;
use Page\Store\Basket;
use Page\Store\Login;

class LoginContext implements SnippetAcceptingContext
{
    /**
     * @var Login
     */
    protected $loginPage;

    /**
     * @var Basket
     */
    protected $basketPage;

    protected $amazonPassword = 'eZhV5fyirWImL7OzIJ9t';

    public function __construct(Login $loginPage, Basket $basketPage)
    {
        $this->customerFixture = new CustomerFixture;
        $this->loginPage       = $loginPage;
        $this->basketPage      = $basketPage;
    }

    /**
     * @Given :email is logged in
     */
    public function isLoggedIn($email)
    {
        $this->loginPage->open();
        $this->loginPage->loginCustomer($email, $this->customerFixture->getDefaultPassword());
    }

    /**
     * @Given I login with amazon as :email
     */
    public function iLoginWithAmazonAs($email)
    {
        $this->loginPage->open();
        $this->loginPage->loginAmazonCustomer($email, $this->getAmazonPassword());
    }


    /**
     * @Given I login with amazon on the basket page as :email
     */
    public function iLoginWithAmazonOnTheBasketPageAs($email)
    {
        $this->basketPage->open();
        $this->basketPage->loginAmazonCustomer($email, $this->getAmazonPassword());
    }

    protected function getAmazonPassword()
    {
        return $this->amazonPassword;
    }
}