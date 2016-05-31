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

abstract class AbstractAmazonCaptureResponse
{
    /**
     * @var AmazonCaptureStatus
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $transactionId;

    /**
     * AbstractAmazonCaptureResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, AmazonCaptureStatusFactory $amazonCaptureStatusFactory)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data[$this->getResultKey()]['CaptureDetails'];

        $status       = $details['CaptureStatus'];
        $this->status = $amazonCaptureStatusFactory->create([
            'state'      => $status['State'],
            'reasonCode' => (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        ]);

        if (isset($details['AmazonCaptureId'])) {
            $this->transactionId = $details['AmazonCaptureId'];
        }
    }

    /**
     * Get status
     *
     * @return AmazonCaptureStatus
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

    /**
     * Get result key
     *
     * @return string
     */
    abstract protected function getResultKey();
}