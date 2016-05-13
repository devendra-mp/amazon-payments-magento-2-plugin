<?php

namespace Amazon\Payment\Domain\Validator;

use Amazon\Payment\Domain\AmazonAuthorizationDetailsResponse;
use Amazon\Payment\Domain\AmazonAuthorizationStatus;
use Amazon\Payment\Exception\AuthorizationTimeoutException;
use Magento\Framework\Exception\StateException;

class AmazonPreCapture
{
    public function validate(AmazonAuthorizationDetailsResponse $response)
    {
        $status = $response->getStatus();

        switch ($status->getReasonCode()) {
            case AmazonAuthorizationStatus::REASON_TRANSACTION_TIMEOUT:
            case AmazonAuthorizationStatus::REASON_SELLER_CLOSED:
                throw new AuthorizationTimeoutException();
        }
    }
}