<?php

namespace Amazon\Login\Api\Data;

interface CustomerLinkInterface
{
    public function setAmazonId($amazonId);

    public function getAmazonId();

    public function setCustomerId($customerId);

    public function getCustomerId();
}