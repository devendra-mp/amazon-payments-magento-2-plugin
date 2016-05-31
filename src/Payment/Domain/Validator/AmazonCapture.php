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

use Amazon\Payment\Domain\AmazonCaptureResponse;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Amazon\Payment\Exception\CapturePendingException;
use Magento\Framework\Exception\StateException;

class AmazonCapture
{
    /**
     * Validate AmazonCaptureResponse
     *
     * @param AmazonCaptureResponse $response
     *
     * @return bool
     * @throws CapturePendingException
     * @throws StateException
     */
    public function validate(AmazonCaptureResponse $response)
    {
        $status = $response->getStatus();

        switch ($status->getState()) {
            case AmazonCaptureStatus::STATE_COMPLETED:
                return true;
            case AmazonCaptureStatus::STATE_PENDING:
                throw new CapturePendingException();
            case AmazonCaptureStatus::STATE_DECLINED:
                throw new StateException(__('Amazon capture declined : %1', $status->getReasonCode()));
        }

        throw new StateException(
            __('Amazon capture invalid state : %1 with reason %2', [$status->getState(), $status->getReasonCode()])
        );
    }
}