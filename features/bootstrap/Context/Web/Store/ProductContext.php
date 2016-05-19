<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Basket;
use Page\Store\Product;

class ProductContext implements SnippetAcceptingContext
{
    /**
     * @var Product
     */
    protected $productPage;

    /**
     * @var Basket
     */
    protected $basketPage;

    /**
     * @param Product $productPage
     * @param Basket $basketPage
     */
    public function __construct(Product $productPage, Basket $basketPage)
    {
        $this->productPage = $productPage;
        $this->basketPage = $basketPage;
    }

    /**
     * @Given I am on a Product Page
     */
    public function iAmOnAProductPage()
    {
        $this->productPage->openWithProductId(1);
    }
}
