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

use Amazon\Payment\Api\Data\PendingRefundInterface;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Model\QueuedRefundUpdaterFactory;
use Amazon\Payment\Model\ResourceModel\PendingRefund\CollectionFactory;
use Magento\Framework\Data\Collection;

class ProcessAmazonRefunds
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var CollectionFactory
     */
    protected $queuedRefundsCollectionFactory;

    /**
     * @var PaymentManagementInterface
     */
    protected $paymentManagement;

    /**
     * @var QueuedRefundUpdaterFactory
     */
    protected $queuedRefundUpdater;

    /**
     * @param CollectionFactory $collectionFactory
     * @param PaymentManagementInterface $paymentManagement
     * @param QueuedRefundUpdaterFactory $queuedRefundUpdater
     * @param int $limit
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        PaymentManagementInterface $paymentManagement,
        QueuedRefundUpdaterFactory $queuedRefundUpdater,
        $limit = 100
    ) {
        $this->queuedRefundsCollectionFactory = $collectionFactory;
        $this->paymentManagement = $paymentManagement;

        $limit = (int) $limit;

        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be greater than 1.');
        }

        $this->limit = $limit;
        $this->queuedRefundUpdater = $queuedRefundUpdater;
    }

    public function execute()
    {
        $collection = $this->queuedRefundsCollectionFactory
            ->create()
            ->addOrder(PendingRefundInterface::CREATED_AT, Collection::SORT_ORDER_ASC)
            ->setPageSize($this->limit)
            ->setCurPage(1);

        $queuedRefundUpdater = $this->queuedRefundUpdater->create();

        foreach($collection->getIdGenerator() as $pendingRefundId) {
            $queuedRefundUpdater->checkAndUpdateRefund($pendingRefundId);
        }
    }
}
