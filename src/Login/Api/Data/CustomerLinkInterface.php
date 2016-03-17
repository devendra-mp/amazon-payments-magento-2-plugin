<?php

namespace Amazon\Login\Api\Data;

interface CustomerLinkInterface
{
    /**
     * Set amazon id
     *
     * @param integer $amazonId
     *
     * @return $this
     */
    public function setAmazonId($amazonId);

    /**
     * Get amazon id
     *
     * @return string
     */
    public function getAmazonId();

    /**
     * Set customer id
     *
     * @param integer $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get customer id
     *
     * @return integer
     */
    public function getCustomerId();

    /**
     * Save customer link
     *
     * @return $this
     */
    public function save();
}