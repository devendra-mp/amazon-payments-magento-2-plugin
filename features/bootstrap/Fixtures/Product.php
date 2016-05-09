<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Context\Data\FixtureContext;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;

class Product extends BaseFixture
{
    protected $defaults
        = [
            ProductInterface::NAME             => 'Test Product',
            ProductInterface::ATTRIBUTE_SET_ID => 4,
            ProductInterface::TYPE_ID          => Type::TYPE_SIMPLE,
            ProductInterface::STATUS           => Status::STATUS_ENABLED,
            ProductInterface::PRICE            => 100.00,
            ProductInterface::VISIBILITY       => 4,
            'stock_data'                       => [
                'qty'          => 10,
                'is_in_stock'  => 1,
                'manage_stock' => 1
            ]
        ];

    /**
     * @var ProductRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository    = $this->getMagentoObject(ProductRepositoryInterface::class);
    }

    public function create(array $data)
    {
        $data        = array_merge($this->defaults, $data);
        $productData = $this->createMagentoObject(ProductInterface::class, ['data' => $data]);

        /**
         * @todo: fix save of stock data
         */
        $product = $this->repository->save($productData);

        FixtureContext::trackFixture($product, $this->repository);

        return $product;
    }

    public function get($sku, $forceReload = false)
    {
        return $this->repository->get($sku, false, null, $forceReload);
    }
}