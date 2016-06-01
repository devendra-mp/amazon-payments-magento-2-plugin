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
namespace Amazon\Login\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

class CustomerSession
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    protected $fullCheckoutUrl;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        $this->fullCheckoutUrl = $urlBuilder->getUrl('checkout/index/index');
    }

    /**
     * Convert the full checkout route path to use just the first part.
     *
     * @param Session $subject
     * @param string $beforeAuthUrl
     * @return array
     */
    public function beforeSetBeforeAuthUrl(Session $subject, $beforeAuthUrl)
    {
        if ($this->fullCheckoutUrl === $beforeAuthUrl) {
            $beforeAuthUrl = $this->urlBuilder->getUrl('checkout');
        }

        return [ $beforeAuthUrl ];
    }
}
