<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\Catalog\Model\Product as ProductModel;

class Product extends BaseFixture
{
    /**
     * @param  string $sku
     *
     * @return ProductModel
     */
    public function getProductBySku($sku)
    {
        $productModel = $this->createMagentoObject(ProductModel::class);
        $product = $productModel->loadByAttribute('sku', $sku);

        if (!$product || is_null($product->getId())) {
            throw new \LogicException("Product {$sku} could not be loaded or found");
        }

        return $product->load($product->getId());
    }

    /**
     * @param  string $sku
     *
     * @return int
     */
    public function getProductIdBySku($sku)
    {
        $product = $this->getProductBySku($sku);
        return $product->getId();
    }
}
