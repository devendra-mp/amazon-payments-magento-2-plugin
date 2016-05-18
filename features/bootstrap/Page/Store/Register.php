<?php

namespace Page\Store;

use Page\AmazonLoginTrait;
use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Register extends Page
{
    use PageTrait, AmazonLoginTrait;

    protected $path = '/customer/account/create';

    protected $elements
        = [
            'open-amazon-login' => ['css' => '#OffAmazonPaymentsWidgets0']
        ];
}