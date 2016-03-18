<?php

namespace Amazon\Login\Api;

use Amazon\Core\Domain\AmazonCustomer;
use Magento\Customer\Api\Data\CustomerInterface;

interface CustomerManagerInterface
{
    /**
     * @param AmazonCustomer $amazonCustomer
     *
     * @return CustomerInterface|null
     */
    public function create(AmazonCustomer $amazonCustomer);

    /**
     * @param integer $customerId
     * @param string  $amazonId
     *
     * @return void
     */
    public function updateLink($customerId, $amazonId);
}