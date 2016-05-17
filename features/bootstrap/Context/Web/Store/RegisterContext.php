<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Register;
use PHPUnit_Framework_Assert;

class RegisterContext implements SnippetAcceptingContext
{
    /**
     * @var Register
     */
    protected $registerPage;

    public function __construct(Register $registerPage)
    {
        $this->registerPage = $registerPage;
    }

    /**
     * @Given I go to register
     */
    public function iGoToRegister()
    {
        $this->registerPage->open();
    }

    /**
     * @Then I see a login with amazon button on the registration page
     */
    public function iSeeALoginWithAmazonButtonOnTheRegistrationPage()
    {
        $hasLwa = $this->registerPage->hasLoginWithAmazonButton();
        PHPUnit_Framework_Assert::assertTrue($hasLwa);
    }
}