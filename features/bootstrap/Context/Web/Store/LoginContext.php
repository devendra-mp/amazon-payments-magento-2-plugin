<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Page\Store\Basket;
use Page\Store\Login;
use Page\Store\Product;
use PHPUnit_Framework_Assert;

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

    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    /**
     * @var Product
     */
    protected $productPage;

    /**
     * @param Login $loginPage
     * @param Basket $basketPage
     * @param Product $productPage
     */
    public function __construct(Login $loginPage, Basket $basketPage, Product $productPage)
    {
        $this->customerFixture = new CustomerFixture;
        $this->loginPage       = $loginPage;
        $this->basketPage      = $basketPage;
        $this->productPage     = $productPage;
    }

    /**
     * @Given I go to login
     */
    public function iGoToLogin()
    {
        $this->loginPage->open();
    }

    /**
     * @Then I see a login with amazon button on the login page
     */
    public function iSeeALoginWithAmazonButtonOnTheLoginPage()
    {
        $hasLwa = $this->loginPage->hasLoginWithAmazonButton();
        PHPUnit_Framework_Assert::assertTrue($hasLwa);
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
        $this->customerFixture->track($email);
    }

    /**
     * @Given I login with Amazon as :email on Product Page ID :productId
     */
    public function iLoginWithAmazonAsOnProductPageId($email, $productId)
    {
        $this->productPage->openWithProductId($productId);
        $this->productPage->loginAmazonCustomer($email, $this->getAmazonPassword());
        $this->customerFixture->track($email);
    }

    /**
     * @Given I login with amazon on the basket page as :email
     */
    public function iLoginWithAmazonOnTheBasketPageAs($email)
    {
        $this->basketPage->open();
        $this->basketPage->loginAmazonCustomer($email, $this->getAmazonPassword());
        $this->customerFixture->track($email);
    }

    protected function getAmazonPassword()
    {
        return $this->amazonPassword;
    }
}
