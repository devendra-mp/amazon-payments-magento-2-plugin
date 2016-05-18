<?php

namespace Page\Store;

use Page\AmazonLoginTrait;
use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Basket extends Page
{
    use PageTrait, AmazonLoginTrait;

    protected $elements
        = [
            'open-amazon-login' => ['css' => '#OffAmazonPaymentsWidgets0'],
            'amazon-login'      => ['css' => 'button']
        ];

    protected $path = '/checkout/cart/';
}