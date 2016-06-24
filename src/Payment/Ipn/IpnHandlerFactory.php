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
namespace Amazon\Payment\Ipn;

use Amazon\Core\Model\EnvironmentChecker;
use Magento\Framework\ObjectManagerInterface;
use PayWithAmazon\IpnHandlerInterface;

class IpnHandlerFactory implements IpnHandlerFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * @var EnvironmentChecker
     */
    protected $environmentChecker;

    public function __construct(
        ObjectManagerInterface $objectManager,
        EnvironmentChecker $environmentChecker,
        $instanceName = '\\PayWithAmazon\\IpnHandlerInterface'
    ) {
        $this->objectManager      = $objectManager;
        $this->instanceName       = $instanceName;
        $this->environmentChecker = $environmentChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function create($headers, $body)
    {
        if ($this->environmentChecker->isTestMode()) {
            return new MockIpnHandler($headers, $body);
        }

        return $this->objectManager->create(
            $this->instanceName,
            ['headers' => $headers, 'body' => $body]
        );
    }
}
