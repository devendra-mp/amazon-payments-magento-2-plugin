<?php

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