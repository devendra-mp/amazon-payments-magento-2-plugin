<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Product as ProductFixture;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;

class ProductContext implements SnippetAcceptingContext
{
    /**
     * @var ProductFixture
     */
    protected $productFixture;

    public function __construct()
    {
        $this->productFixture = new ProductFixture;
    }

    /**
     * @Given there is a product with sku :sku
     */
    public function thereIsAProductWithSku($sku)
    {
        $this->productFixture->create([ProductInterface::SKU => $sku]);
    }
}