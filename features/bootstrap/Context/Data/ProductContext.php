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
namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Bex\Behat\Magento2InitExtension\Fixtures\MagentoConfigManager;
use Context\Data\ConfigContext;
use Fixtures\Product as ProductFixture;
use Magento\Catalog\Api\Data\ProductInterface;

class ProductContext implements SnippetAcceptingContext
{
    /**
     * @var ProductFixture
     */
    protected $productFixture;

    /**
     * @var ConfigContext
     */
    protected $configContext;

    public function __construct()
    {
        $this->productFixture = new ProductFixture;
        $this->configContext = new ConfigContext;
    }

    /**
     * @Given there is a product with sku :sku
     */
    public function thereIsAProductWithSku($sku)
    {
        $this->productFixture->create([ProductInterface::SKU => $sku]);
    }

    /**
     * @Given Product ID :productId belongs to an excluded category
     */
    public function productIdBelongsToAnExcludedCategory($productId)
    {
        $product = $this->productFixture->getById((int) $productId);

        $productCategories = $product->getCategoryIds();

        if (empty($productCategories)) {
            throw new \RuntimeException(
                "Product ID $productId has no associated categories. Please choose another one."
            );
        }

        $this->configContext->changeConfig(
            'payment/amazon_payment/excluded_categories',
            implode(',', $productCategories)
        );
    }

    /**
     * @Given Product ID :productId does not belong to an excluded category
     */
    public function productIDDoesNotBelongToAnExcludedCategory($productId)
    {
        $product = $this->productFixture->getById((int) $productId);

        $productCategories = $product->getCategoryIds();

        if (empty($productCategories)) {
            throw new \RuntimeException(
                "Product ID $productId has no associated categories. Please choose another one."
            );
        }

        $this->configContext->changeConfig(
            'payment/amazon_payment/excluded_categories',
            implode(',', $productCategories)
        );
    }
}
