<?php

namespace Amazon\Payment\Api;

interface PaymentManagementInterface
{
    /**
     * @param string $authorizationId
     *
     * @return void
     */
    public function capturePendingAuthorization($authorizationId);
}