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
namespace Amazon\Payment\Cron;

use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Model\ResourceModel\PendingCapture\CollectionFactory;
use Magento\Framework\Data\Collection;

class GetAmazonCaptureUpdates
{
    /**
     * @var int
     */
    protected $limit = 100;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var PaymentManagementInterface
     */
    protected $paymentManagement;

    public function __construct(
        CollectionFactory $collectionFactory,
        PaymentManagementInterface $paymentManagement
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->paymentManagement = $paymentManagement;
    }

    public function execute()
    {
        $collection = $this->collectionFactory
            ->create()
            ->addOrder(PendingCaptureInterface::CREATED_AT, Collection::SORT_ORDER_ASC)
            ->setPageSize($this->limit)
            ->setCurPage(1);

        $pendingCaptureIds = $collection->getIdGenerator();
        foreach($pendingCaptureIds as $pendingCaptureId) {
            $this->paymentManagement->updateCapture($pendingCaptureId);
        }
    }
}