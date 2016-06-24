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
namespace Amazon\Core\Observer;

use Amazon\Core\Model\Validation\ApiCredentialsValidatorFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class PaymentConfigSaveAfter implements ObserverInterface
{
    /**
     * @var ApiCredentialsValidatorFactory
     */
    protected $apiCredentialsValidatorFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param ApiCredentialsValidatorFactory $apiCredentialsValidatorFactory
     * @param ManagerInterface               $messageManager
     */
    public function __construct(
        ApiCredentialsValidatorFactory $apiCredentialsValidatorFactory,
        ManagerInterface $messageManager
    ) {
        $this->apiCredentialsValidatorFactory = $apiCredentialsValidatorFactory;
        $this->messageManager                 = $messageManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @see \Magento\Config\Model\Config::save() */
        $validator = $this->apiCredentialsValidatorFactory->create();

        $messageManagerMethod = 'addError';

        if ($validator->isValid($observer->getStore())) {
            $messageManagerMethod = 'addSuccess';
        }

        foreach ($validator->getMessages() as $message) {
            $this->messageManager->$messageManagerMethod($message);
        }
    }
}
