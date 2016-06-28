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
namespace Amazon\Core\Client;

use Amazon\Core\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Data
     */
    protected $coreHelper;

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * ClientFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Data                   $coreHelper
     * @param string                 $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $coreHelper,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->coreHelper    = $coreHelper;
        $this->instanceName  = $instanceName;
    }

    /**
     * {@inheritDoc}
     */
    public function create($scope = 'default', $scopeId = null)
    {
        $config = [
            Data::AMAZON_SECRET_KEY  => $this->coreHelper->getSecretKey($scope, $scopeId),
            Data::AMAZON_ACCESS_KEY  => $this->coreHelper->getAccessKey($scope, $scopeId),
            Data::AMAZON_MERCHANT_ID => $this->coreHelper->getMerchantId($scope, $scopeId),
            Data::AMAZON_REGION      => $this->coreHelper->getRegion($scope, $scopeId),
            Data::AMAZON_SANDBOX     => $this->coreHelper->isSandboxEnabled($scope, $scopeId),
            Data::AMAZON_CLIENT_ID   => $this->coreHelper->getClientId($scope, $scopeId)
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}
