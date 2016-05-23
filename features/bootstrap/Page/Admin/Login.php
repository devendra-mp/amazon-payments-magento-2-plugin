<?php

namespace Page\Admin;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Login extends Page
{
    use PageTrait;

    protected $path = '/admin/admin/';

    protected $elements
        = [
            'login' => ['css' => '.action-login'],
        ];

    public function loginAdmin($email, $password)
    {
        $this->fillField('login[username]', $email);
        $this->fillField('login[password]', $password);
        $this->clickElement('login');
    }
}