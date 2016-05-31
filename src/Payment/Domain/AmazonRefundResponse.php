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
namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

class AmazonRefundResponse
{
    /**
     * @var AmazonRefundStatus
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $transactionId;

    /**
     * AmazonRefundResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, AmazonRefundStatusFactory $amazonRefundStatusFactory)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['RefundResult']['RefundDetails'];

        $status       = $details['RefundStatus'];
        $this->status = $amazonRefundStatusFactory->create([
            'state'      => $status['State'],
            'reasonCode' => (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        ]);

        if (isset($details['AmazonRefundId'])) {
            $this->transactionId = $details['AmazonRefundId'];
        }
    }

    /**
     * Get status
     *
     * @return AmazonRefundStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get transaction id
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}