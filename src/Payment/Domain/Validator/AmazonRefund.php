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

use Amazon\Payment\Domain\AmazonRefundResponse;
use Amazon\Payment\Domain\AmazonRefundStatus;
use Magento\Framework\Exception\StateException;

class AmazonRefund
{
    /**
     * Validate AmazonRefundResponse
     *
     * @param AmazonRefundResponse $response
     *
     * @return bool
     * @throws StateException
     */
    public function validate(AmazonRefundResponse $response)
    {
        $status = $response->getStatus();

        switch ($status->getState()) {
            case AmazonRefundStatus::STATE_COMPLETED:
            case AmazonRefundStatus::STATE_PENDING:
                return true;
        }

        throw new StateException(
            __('Amazon refund invalid state : %1 with reason %2', [$status->getState(), $status->getReasonCode()])
        );
    }
}