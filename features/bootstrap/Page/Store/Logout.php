<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Logout extends Page
{
    use PageTrait;

    protected $path = '/customer/account/logout';

    public function logout()
    {
        $this->open();
        $this->waitForCondition('true === false', 5000);
    }

    protected function verifyUrl(array $urlParameters = array())
    {
        return true;
    }
}