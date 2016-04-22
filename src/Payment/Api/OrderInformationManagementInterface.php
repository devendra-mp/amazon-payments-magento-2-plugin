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

    /**
     * @param $amazonOrderReferenceId
     *
     * @return boolean
     */
    public function closeOrderReference($amazonOrderReferenceId);

    /**
     * @param $amazonOrderReferenceId
     *
     * @return boolean
     */
    public function cancelOrderReference($amazonOrderReferenceId);
}
