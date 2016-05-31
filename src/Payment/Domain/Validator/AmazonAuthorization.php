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
namespace Amazon\Payment\Domain\Validator;

use Amazon\Payment\Domain\AmazonAuthorizationResponse;
use Amazon\Payment\Domain\AmazonAuthorizationStatus;
use Amazon\Payment\Exception\HardDeclineException;
use Amazon\Payment\Exception\SoftDeclineException;
use Magento\Framework\Exception\StateException;

class AmazonAuthorization
{
    /**
     * Validate AmazonAuthorizationResponse
     *
     * @param AmazonAuthorizationResponse $response
     *
     * @return bool
     * @throws HardDeclineException
     * @throws SoftDeclineException
     * @throws StateException
     */
    public function validate(AmazonAuthorizationResponse $response)
    {
        $status = $response->getStatus();

        switch ($status->getState()) {
            case AmazonAuthorizationStatus::STATE_CLOSED:
                switch ($status->getReasonCode()) {
                    case AmazonAuthorizationStatus::REASON_MAX_CAPTURES_PROCESSED:
                        return true;
                }
            case AmazonAuthorizationStatus::STATE_OPEN:
                return true;
            case AmazonAuthorizationStatus::STATE_DECLINED:
                $this->throwDeclinedExceptionForStatus($status);
        }

        throw new StateException($this->getExceptionMessage($status));
    }

    protected function throwDeclinedExceptionForStatus(AmazonAuthorizationStatus $status)
    {
        switch ($status->getReasonCode()) {
            case AmazonAuthorizationStatus::REASON_AMAZON_REJECTED:
            case AmazonAuthorizationStatus::REASON_TRANSACTION_TIMEOUT:
            case AmazonAuthorizationStatus::REASON_PROCESSING_FAILURE:
                throw new HardDeclineException($this->getExceptionMessage($status));
            case AmazonAuthorizationStatus::REASON_INVALID_PAYMENT_METHOD:
                throw new SoftDeclineException($this->getExceptionMessage($status));
        }
    }

    protected function getExceptionMessage(AmazonAuthorizationStatus $status)
    {
        return __('Amazon authorize invalid state : %1 with reason %2', $status->getState(), $status->getReasonCode());
    }
}