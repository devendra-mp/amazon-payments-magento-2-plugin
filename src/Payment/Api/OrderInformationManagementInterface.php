<?php

namespace Amazon\Payment\Api;

interface OrderInformationManagementInterface
{
    /**
     * @param string $amazonOrderReferenceId
     *
     * @return boolean
     */
    public function saveOrderInformation($amazonOrderReferenceId);

    /**
     * @param $amazonOrderReferenceId
     *
     * @return boolean
     */
    public function confirmOrderReference($amazonOrderReferenceId);
}
