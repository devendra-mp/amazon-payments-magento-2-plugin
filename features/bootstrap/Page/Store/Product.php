<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Product extends Page
{
    use PageTrait;

    protected $path = '/catalog/product/view/id/{id}';

    protected $elements
        = [
            'add-to-cart'     => ['css' => '#product-addtocart-button'],
            'success-message' => ['css' => '.message-success']
        ];

    public function addToBasket()
    {
        $this->getElement('add-to-cart')->click();
        $this->waitForElement('success-message');
    }
}