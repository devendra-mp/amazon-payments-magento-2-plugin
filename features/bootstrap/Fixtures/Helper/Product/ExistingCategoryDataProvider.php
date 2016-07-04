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
namespace Fixtures\Helper\Product;

use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use \Magento\Framework\App\ObjectManager;

class ExistingCategoryDataProvider implements ProductDataProvider
{
    /**
     * @var int|null
     */
    private $categoryId;

    /**
     * @param ProductInterface $product
     */
    public function addDataToProduct(ProductInterface $product)
    {
        /** @var CategoryManagementInterface $categoryManagement */
        $categoryManagement = ObjectManager::getInstance()->get(CategoryManagementInterface::class);

        $category = $categoryManagement->getTree($this->categoryId, 0);

        $product->setData('category_ids', [$category->getId()]);
    }

    /**
     * @param int|null $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }
}
