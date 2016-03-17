<?php

namespace Amazon\Login\Api\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Magento\Customer\Api\Data\CustomerInterface;

interface CompositeMatcherInterface
{
    /**
     * @param AmazonCustomer $amazonCustomer
     *
     * @return CustomerInterface|null
     */
    public function match(AmazonCustomer $amazonCustomer);
}