<?php

namespace Page\Store;

use Page\AmazonLoginTrait;
use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Product extends Page
{
    use PageTrait, AmazonLoginTrait;

    protected $path = '/catalog/product/view/id/{id}';

    protected $elements
        = [
            'add-to-cart'     => ['css' => '#product-addtocart-button'],
            'success-message' => ['css' => '.message-success'],
            'open-amazon-login' => ['css' => '#OffAmazonPaymentsWidgets0'],
            'amazon-login'      => ['css' => 'button'],
        ];

    /**
     * @param int $productId
     */
    public function openWithProductId($productId)
    {
        $this->open(['id' => (int) $productId]);
    }

    public function addToBasket()
    {
        $this->getElement('add-to-cart')->click();
        $this->waitForElement('success-message');
    }
}
