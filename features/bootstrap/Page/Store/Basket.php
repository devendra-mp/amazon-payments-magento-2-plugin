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

class Basket extends Page
{
    use PageTrait, AmazonLoginTrait;

    protected $elements
        = [
            'open-amazon-login' => ['css' => '#OffAmazonPaymentsWidgets0'],
            'amazon-login'      => ['css' => 'button']
        ];

    protected $path = '/checkout/cart/';

    /**
     * @return bool
     */
    public function pwaButtonIsVisibleNoWait()
    {
        try {
            return $this->getElement('open-amazon-login')->isVisible();
        } catch (\Exception $e) {
            return false;
        }
    }
}
