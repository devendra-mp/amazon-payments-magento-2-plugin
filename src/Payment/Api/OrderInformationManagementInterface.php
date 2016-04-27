<?php

namespace Amazon\Payment\Api;

use Magento\Framework\Exception\LocalizedException;

interface OrderInformationManagementInterface
{
    /**
     * @param string $amazonOrderReferenceId
     * @param array $allowedConstraints
     *
     * @throws LocalizedException
     */
    public function saveOrderInformation($amazonOrderReferenceId, $allowedConstraints = []);

    /**
     * @param $amazonOrderReferenceId
     *
     * @throws LocalizedException
     */
    public function confirmOrderReference($amazonOrderReferenceId);

    /**
     * @param $amazonOrderReferenceId
     *
     * @throws LocalizedException
     */
    public function closeOrderReference($amazonOrderReferenceId);

    /**
     * @param $amazonOrderReferenceId
     *
     * @throws LocalizedException
     */
    public function cancelOrderReference($amazonOrderReferenceId);
}
