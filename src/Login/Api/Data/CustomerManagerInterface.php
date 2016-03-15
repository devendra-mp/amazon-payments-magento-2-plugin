<?php

namespace Amazon\Login\Api\Data;

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
}