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
    public function getShippingAddress($amazonOrderReferenceId, $addressConsentToken);

    /**
     * @param string $amazonOrderReferenceId
     * @param string $addressConsentToken
     *
     * @return array
     */
    public function getBillingAddress($amazonOrderReferenceId, $addressConsentToken);
}