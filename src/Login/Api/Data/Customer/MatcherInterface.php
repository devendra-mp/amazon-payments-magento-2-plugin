<?php

namespace Amazon\Login\Api\Data\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Magento\Customer\Api\Data\CustomerInterface;

interface MatcherInterface
{
    /**
     * @param AmazonCustomer $amazonCustomer
     *
     * @return CustomerInterface|null
     */
    public function match(AmazonCustomer $amazonCustomer);
}