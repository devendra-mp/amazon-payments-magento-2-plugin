<?php

namespace Amazon\Login\Domain;

class ValidationCredentials
{
    protected $customerId;

    protected $amazonId;

    public function __construct($customerId, $amazonId)
    {
        $this->customerId = $customerId;
        $this->amazonId = $amazonId;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function getAmazonId()
    {
        return $this->amazonId;
    }
}