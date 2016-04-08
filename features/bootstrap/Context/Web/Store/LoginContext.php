<?php

namespace Context\Web\Store;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Login;

class LoginContext implements SnippetAcceptingContext
{
    /**
     * @var Login
     */
    protected $loginPage;

    public function __construct(Login $loginPage)
    {
        $this->loginPage = $loginPage;
    }

    /**
     * @Given :email is logged in
     */
    public function isLoggedIn($email)
    {
    }

    /**
     * @When I login with amazon as :email
     */
    public function iLoginWithAmazonAs($email)
    {
    }

    /**
     * @Then :email is associated with an amazon account
     */
    public function isAssociatedWithAnAmazonAccount($email)
    {
    }
}