<?php

namespace Page\Store;

use Page\AmazonLoginTrait;
use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    use PageTrait, AmazonLoginTrait;

    protected $path = '/customer/account/login';

    protected $elements
        = [
            'login'             => '#send2',
            'open-amazon-login' => '#OffAmazonPaymentsWidgets0',
            'amazon-login'      => ['css' => 'button']
        ];

    public function loginCustomer($email, $password)
    {
        $this->fillField('email', $email);
        $this->fillField('pass', $password);
        $this->getElement('login')->click();
    }
}