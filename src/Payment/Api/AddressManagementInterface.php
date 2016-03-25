<?php

namespace Amazon\Payment\Api;

interface AddressManagementInterface
{
    /**
     * @param string $amazonOrderReferenceId
     * @param string $addressConsentToken
     *
     * @return array
     */
    public function saveShippingAddress($amazonOrderReferenceId, $addressConsentToken);
}