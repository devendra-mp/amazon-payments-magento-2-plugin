<?php

namespace Page\Store;

use Page\Type\StorePage;

class ProductPage extends StorePage
{
    protected $path = "/catalog/product/view/id/{id}";

    protected $elements = [
        'Add to cart button' => ['css' => '#product-addtocart-button']
    ];

    public function addToBasket()
    {
        $this->clickElement('Add to cart button');
        $this->waitForPageLoad();
    }
}
