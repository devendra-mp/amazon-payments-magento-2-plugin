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

use Amazon\Payment\Domain\AmazonAuthorizationResponse;
use Amazon\Payment\Domain\AmazonCaptureResponse;
use Magento\Sales\Api\Data\OrderInterface;
use Amazon\Payment\Domain\AmazonRefundResponse;
use Magento\Payment\Model\InfoInterface;

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
     * Update authorization
     *
     * @param integer $pendingAuthorizationId
     *
     * @return void
     */
    public function updateAuthorization($pendingAuthorizationId);

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
     * Queue pending authorization
     *
     * @param AmazonAuthorizationResponse $response
     * @param OrderInterface              $order
     *
     * @return void
     */
    public function queuePendingAuthorization(AmazonAuthorizationResponse $response, OrderInterface $order);

    /**
     * Queue pending refund
     *
     * @param AmazonRefundResponse $response
     * @param InfoInterface        $payment
     *
     * @return void
     */
    public function queuePendingRefund(AmazonRefundResponse $response, InfoInterface $payment);

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
