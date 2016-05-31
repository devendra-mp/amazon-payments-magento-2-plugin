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
use Amazon\Core\Model\EnvironmentChecker;
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
     * @var EnvironmentChecker
     */
    protected $environmentChecker;

    /**
     * ClientFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Data $coreHelper
     * @param EnvironmentChecker $environmentChecker
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $coreHelper,
        EnvironmentChecker $environmentChecker,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager      = $objectManager;
        $this->coreHelper         = $coreHelper;
        $this->environmentChecker = $environmentChecker;
        $this->instanceName       = $instanceName;
    }

    /**
     * {@inheritDoc}
     */
    public function create($storeId = null)
    {
        $config = [
            'secret_key'  => $this->coreHelper->getSecretKey(ScopeInterface::SCOPE_STORE, $storeId),
            'access_key'  => $this->coreHelper->getAccessKey(ScopeInterface::SCOPE_STORE, $storeId),
            'merchant_id' => $this->coreHelper->getMerchantId(ScopeInterface::SCOPE_STORE, $storeId),
            'region'      => $this->coreHelper->getRegion(ScopeInterface::SCOPE_STORE, $storeId),
            'sandbox'     => $this->coreHelper->isSandboxEnabled(ScopeInterface::SCOPE_STORE, $storeId),
            'client_id'   => $this->coreHelper->getClientId(ScopeInterface::SCOPE_STORE, $storeId)
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}
