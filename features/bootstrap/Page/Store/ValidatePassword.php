<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class ValidatePassword extends Page
{
    use PageTrait;

    protected $path = '/amazon/login/validate';

    protected $elements
        = [
            'submit-password' => ['css' => 'button.submit']
        ];

    public function submitWithPassword($password)
    {
        $this->fillField('password', $password);
        $this->clickElement('submit-password');
    }
}