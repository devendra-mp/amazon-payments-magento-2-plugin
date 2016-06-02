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
namespace Amazon\Payment\Api;

use Amazon\Payment\Domain\AmazonCaptureResponse;

interface PaymentManagementInterface
{
    /**
     * Update capture
     *
     * @param integer $pendingCaptureId
     *
     * @return void
     */
    public function updateCapture($pendingCaptureId);

    /**
     * Queue pending capture
     *
     * @param AmazonCaptureResponse $response
     * @param integer               $paymentId
     * @param integer               $orderId
     *
     * @return void
     */
    public function queuePendingCapture(AmazonCaptureResponse $response, $paymentId, $orderId);

    /**
     * Close transaction
     *
     * @param string  $transactionId
     * @param integer $paymentId
     * @param integer $orderId
     *
     * @return void
     */
    public function closeTransaction($transactionId, $paymentId, $orderId);
}