<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Product;

class BasketContext implements SnippetAcceptingContext
{
    /**
     * @var Product
     */
    protected $productPage;

    public function __construct(Product $productPage)
    {
        $this->productPage    = $productPage;
    }

    /**
     * @Given there is a valid product in my basket
     */
    public function thereIsAValidProductInMyBasket()
    {
        $this->productPage->open([
            'id' => 1
        ]);
        $this->productPage->addToBasket();
    }
}