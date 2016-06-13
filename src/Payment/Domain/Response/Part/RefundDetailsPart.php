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
namespace Amazon\Payment\Domain\Response\Part;

use Amazon\Payment\Domain\AmazonRefundStatus;
use Amazon\Payment\Domain\AmazonRefundStatusFactory;

class RefundDetailsPart implements PartInterface
{
    /**
     * @var AmazonRefundStatus
     */
    protected $refundStatus;

    /**
     * @var string|null
     */
    protected $refundId;

    /**
     * @param AmazonRefundStatusFactory $amazonRefundStatusFactory
     * @param array $rawRefundDetails
     */
    public function __construct(
        AmazonRefundStatusFactory $amazonRefundStatusFactory,
        array $rawRefundDetails = []
    ) {
        $statusData = $rawRefundDetails['RefundStatus'];

        $this->refundStatus = $amazonRefundStatusFactory->create([
            'state'      => $statusData['State'],
            'reasonCode' => isset($statusData['ReasonCode']) ? $statusData['ReasonCode'] : null
        ]);

        if (isset($rawRefundDetails['AmazonRefundId'])) {
            $this->refundId = $rawRefundDetails['AmazonRefundId'];
        }
    }

    /**
     * @return AmazonRefundStatus
     */
    public function getRefundStatus()
    {
        return $this->refundStatus;
    }

    /**
     * @return string|null
     */
    public function getRefundId()
    {
        return $this->refundId;
    }

    /**
     * @return bool
     */
    public function isRefundPending()
    {
        return $this->refundStatus->getState() === AmazonRefundStatus::STATE_PENDING;
    }

    /**
     * @return bool
     */
    public function isRefundCompleted()
    {
        return $this->refundStatus->getState() === AmazonRefundStatus::STATE_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isRefundDeclined()
    {
        return $this->refundStatus->getState() === AmazonRefundStatus::STATE_DECLINED;
    }
}
