<?php
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
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