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

    /**
     * @param  string $sku
     * @param  int    $expectedQty
     * @param  string $warehouseCode
     */
    public function checkQtyIs($sku, $expectedQty, $warehouseCode)
    {
        $product = $this->getProductBySku($sku);
        $productQty = $this->getProductStockItem($product->getId(), $warehouseCode)->getQty();

        if ($productQty != $expectedQty) {
            throw new \Exception(sprintf("Product qty is %s expected %s", $productQty, $expectedQty));
        }
    }

    /**
     * @param  string $sku
     * @param  int    $expectedStockStatus
     * @param  string $warehouseCode
     */
    public function checkStockStatusIs($sku, $expectedStockStatus, $warehouseCode)
    {
        $product = $this->getProductBySku($sku);
        $stockStatus = $this->getProductStockItem($product->getId(), $warehouseCode)->getIsInStock();

        if ($stockStatus != $expectedStockStatus) {
            throw new \Exception(sprintf("Product stock status is %s expected %s", $stockStatus, $expectedStockStatus));
        }
    }

    /**
     * @param  int    $productId
     * @param  string $warehouseCode
     *
     * @return StockItemInterface
     */
    protected function getProductStockItem($productId, $warehouseCode)
    {
        $stockRegistryProvider = $this->createMagentoObject(StockRegistryProviderInterface::class);
        $stockRegistry = $this->createMagentoObject(
            StockRegistryInterface::class,
            ['stockRegistryProvider' => $stockRegistryProvider]
        );

        $websiteIds = ['EU' => 1, 'US' => 4];

        return $stockRegistry->getStockItem($productId, $websiteIds[$warehouseCode]);
    }
}
