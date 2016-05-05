<?php

namespace Amazon\Payment\Model;

use Amazon\Payment\Api\PaymentManagementInterface;

class PaymentManagement implements PaymentManagementInterface
{
    /**
     * {@inheritDoc}
     */
    public function capturePendingAuthorization($authorizationId)
    {
        echo $authorizationId . PHP_EOL;
    }
}