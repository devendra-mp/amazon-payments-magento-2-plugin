<?php

namespace Amazon\Payment\Api;

use Magento\Framework\Exception\ValidatorException;

interface OrderInformationManagementInterface
{
    /**
     * @param string $amazonOrderReferenceId
     * @param array $allowedConstraints
     *
     * @throws ValidatorException
     */
    public function saveOrderInformation($amazonOrderReferenceId, $allowedConstraints = []);

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
