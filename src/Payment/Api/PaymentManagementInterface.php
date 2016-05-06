<?php

namespace Amazon\Payment\Api;

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
}