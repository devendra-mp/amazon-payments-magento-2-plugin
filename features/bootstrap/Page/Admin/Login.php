<?php

namespace Page\Admin;

use Page\Type\AdminPage;

class Login extends AdminPage
{
    protected $path = "/admin/admin/";

    protected $elements = [
        'Username' => ['css' => '#username'],
        'Password' => ['css' => '#login'],
        'Login button' => ['css' => '.action-login']
    ];

    public function login($username, $password)
    {
        $this->setElementValue('Username', $username);
        $this->setElementValue('Password', $password);
        $this->clickElement('Login button');
        $this->waitForPageLoad();
    }
}
