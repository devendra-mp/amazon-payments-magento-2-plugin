<?php

namespace Amazon\Login\Domain;

class ValidationCredentials
{
    /**
     * @var integer
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $amazonId;

    /**
     * ValidationCredentials constructor.
     *
     * @param integer $customerId
     * @param string  $amazonId
     */
    public function __construct($customerId, $amazonId)
    {
        $this->customerId = $customerId;
        $this->amazonId   = $amazonId;
    }

    /**
     * Get customer id
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Get amazon id
     *
     * @return string
     */
    public function getAmazonId()
    {
        return $this->amazonId;
    }
}