<?php
namespace Context\Admin;

use Behat\Behat\Tester\Exception\PendingException;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Admin\Login;

class LoginContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Login
     */
    private $adminLogin;
    /**
     * @param Login $adminLogin
     */
    public function __construct(Login $adminLogin)
    {
        $this->adminLogin = $adminLogin;
    }
    /**
     * @Given I am logged into the magento admin with username :username and password :password
     */
    public function iAmLoggedIntoMagentoWithUserNameAs($username, $password)
    {
        $this->adminLogin->openPage();
        $this->adminLogin->login($username, $password);
    }
    /**
     * @Given I am a site admin
     * @Given I am logged in as admin
     * @When  I login to the admin
     */
    public function iAmASiteAdmin()
    {
        $this->adminLogin->openPage();
        $this->adminLogin->login('admin', 'admin123');
    }
}
