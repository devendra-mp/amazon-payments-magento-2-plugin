<?php

namespace Page\Store;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    protected $path = '/customer/account/login';

    protected $elements = [
        'login' => '#send2'
    ];

    public function loginCustomer($email, $password)
    {
        $this->fillField('email', $email);
        $this->fillField('pass', $password);
        $this->getElement('login')->click();
    }
}