<?php

namespace Amazon\Payment\Model;

use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Model\PendingCaptureFactory;
use Exception;

class PaymentManagement implements PaymentManagementInterface
{
    public function __construct(
        PendingCaptureFactory $pendingCaptureFactory
    ) {
        $this->pendingCaptureFactory = $pendingCaptureFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function capturePendingAuthorization($authorizationId)
    {
        try {
            $pendingCapture = $this->pendingCaptureFactory->create();
            $pendingCapture->getResource()->beginTransaction();
            $pendingCapture->setLockOnLoad(true);
            $pendingCapture->load($authorizationId, PendingCaptureInterface::AUTHORIZATION_ID);

            if ($pendingCapture->getAuthorizationId()) {
                //get status update from amazon and handle
            }

            $pendingCapture->getResource()->commit();
        } catch (Exception $e) {
            $pendingCapture->getResource()->rollBack();
        }
    }
}