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

abstract class AbstractAmazonAuthorizationResponse
{
    /**
     * @var AmazonAuthorizationStatus
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $captureTransactionId;

    /**
     * @var string|null
     */
    protected $authorizeTransactionId;

    /**
     * AmazonAuthorizationResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(
        ResponseInterface $response,
        AmazonAuthorizationStatusFactory $amazonAuthorizationStatusFactory
    ) {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data[$this->getResultKey()]['AuthorizationDetails'];

        $status       = $details['AuthorizationStatus'];
        $this->status = $amazonAuthorizationStatusFactory->create([
            'state'      => $status['State'],
            'reasonCode' => (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        ]);

        if (isset($details['IdList']['member'])) {
            $this->captureTransactionId = $details['IdList']['member'];
        }

        if (isset($details['AmazonAuthorizationId'])) {
            $this->authorizeTransactionId = $details['AmazonAuthorizationId'];
        }
    }

    /**
     * Get status
     *
     * @return AmazonAuthorizationStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get authorize transaction id
     *
     * @return string|null
     */
    public function getAuthorizeTransactionId()
    {
        return $this->authorizeTransactionId;
    }

    /**
     * Get capture transaction id
     *
     * @return string|null
     */
    public function getCaptureTransactionId()
    {
        return $this->captureTransactionId;
    }

    /**
     * Get result key
     *
     * @return string
     */
    abstract protected function getResultKey();
}