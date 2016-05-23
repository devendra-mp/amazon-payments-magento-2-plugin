<?php

namespace Context\Web\Admin;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\AdminUser as AdminUserFixture;
use Page\Admin\Login;
use PHPUnit_Framework_Assert;

class LoginContext implements SnippetAcceptingContext
{
    /**
     * @var AdminUserFixture
     */
    protected $adminUserFixture;

    /**
     * @var Login
     */
    protected $loginPage;

    public function __construct(Login $loginPage)
    {
        $this->loginPage        = $loginPage;
        $this->adminUserFixture = new AdminUserFixture;
    }

    /**
     * @Given I am logged into admin
     */
    public function iAmLoggedIntoAdmin()
    {
        $this->adminUserFixture->generate();

        $this->loginPage->open();

        $this->loginPage->loginAdmin(
            $this->adminUserFixture->getDefaultUsername(),
            $this->adminUserFixture->getDefaultPassword()
        );
    }
}