<?php

namespace Page\Store;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Authorize extends Page
{
    protected $path = '/amazon/login/authorize?access_token={access_token}';

    protected function verify(array $urlParameters)
    {
        $this->verifyResponse();
    }
}