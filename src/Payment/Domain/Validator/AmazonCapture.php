<?php

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