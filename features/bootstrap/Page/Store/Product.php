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
namespace Page\Store;

use Page\AmazonLoginTrait;
use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Product extends Page
{
    use PageTrait, AmazonLoginTrait;

    protected $path = '/catalog/product/view/id/{id}';

    protected $elements
        = [
            'add-to-cart'     => ['css' => '#product-addtocart-button'],
            'success-message' => ['css' => '.message-success'],
            'open-amazon-login' => ['css' => '#OffAmazonPaymentsWidgets0'],
            'amazon-login'      => ['css' => 'button'],
        ];

    /**
     * @param int $productId
     */
    public function openWithProductId($productId)
    {
        $this->open(['id' => (int) $productId]);
    }

    public function addToBasket()
    {
        $this->getElement('add-to-cart')->click();
        $this->waitForElement('success-message');
    }
}
