<?php

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
     *
     * @return void
     */
    public function queuePendingCapture(AmazonCaptureResponse $response);

    /**
     * Close transaction
     *
     * @param string $transactionId
     *
     * @return void
     */
    public function closeTransaction($transactionId);
}