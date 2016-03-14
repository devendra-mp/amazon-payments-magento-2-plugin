<?php

namespace Amazon\Login\Api\Data;

use Amazon\Core\Domain\AmazonCustomer;

interface CustomerManagerInterface
{
    public function create(AmazonCustomer $amazonCustomer);
}