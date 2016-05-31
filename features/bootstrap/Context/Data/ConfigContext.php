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

class ConfigContext implements SnippetAcceptingContext
{
    /**
     * @var MagentoConfigManager
     */
    protected $configManager;

    protected $hasConfigChanges = false;

    public function __construct()
    {
        $this->configManager = new MagentoConfigManager;
    }

    /**
     * @Given Login with Amazon is disabled
     */
    public function loginWithAmazonIsDisabled()
    {
        $this->changeConfig('payment/amazon_payment/lwa_enabled', '0');
    }

    /**
     * @Given orders are charged for at order placement
     */
    public function ordersAreChargedForAtOrderPlacement()
    {
        $this->changeConfig('payment/amazon_payment/payment_action', 'authorize_capture');
    }

    protected function changeConfig($path, $value, $scopeType = 'default', $scopeCode = null)
    {
        $this->configManager->changeConfigs(
            [
                [
                    'path'       => $path,
                    'value'      => $value,
                    'scope_type' => $scopeType,
                    'scope_code' => $scopeCode
                ]
            ]
        );
        
        $this->hasConfigChanges = true;
    }

    /**
     * @AfterScenario
     */
    public function revertConfig()
    {
        if ($this->hasConfigChanges) {
            $this->configManager->revertAllConfig();
            $this->hasConfigChanges = false;
        }
    }
}