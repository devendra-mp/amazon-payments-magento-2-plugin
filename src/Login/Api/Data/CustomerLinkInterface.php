<?php

namespace Amazon\Login\Api\Data;

use Exception;

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
     * @throws Exception
     */
    public function save();

    /**
     * Load customer link data
     *
     * @param integer $modelId
     * @param null|string $field
     * @return $this
     */
    public function load($modelId, $field = null);
}