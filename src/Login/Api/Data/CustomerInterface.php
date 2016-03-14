<?php

namespace Amazon\Login\Api\Data;

interface CustomerInterface
{
    public function setAmazonId($amazonId);

    public function getAmazonId();

    public function setCustomerId($customerId);

    public function getCustomerId();
}