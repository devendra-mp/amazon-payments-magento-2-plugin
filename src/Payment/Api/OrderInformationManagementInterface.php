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
     * @param string $amazonOrderReferenceId
     * @param null|integer $storeId
     *
     * @throws LocalizedException
     */
    public function confirmOrderReference($amazonOrderReferenceId, $storeId = null);

    /**
     * @param string $amazonOrderReferenceId
     * @param null|integer $storeId
     *
     * @throws LocalizedException
     */
    public function closeOrderReference($amazonOrderReferenceId, $storeId = null);

    /**
     * @param string $amazonOrderReferenceId
     * @param null|integer $storeId
     *
     * @throws LocalizedException
     */
    public function cancelOrderReference($amazonOrderReferenceId, $storeId = null);
}
