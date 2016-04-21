<?php

namespace Amazon\Payment\Api\Data;

use Exception;

interface QuoteLinkInterface
{
    /**
     * Set amazon order reference id
     *
     * @param string $amazonOrderReferenceId
     *
     * @return $this
     */
    public function setAmazonOrderReferenceId($amazonOrderReferenceId);

    /**
     * Get amazon order reference id
     *
     * @return string
     */
    public function getAmazonOrderReferenceId();

    /**
     * Set quote id
     *
     * @param integer $quoteId
     *
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Get quote id
     *
     * @return integer
     */
    public function getQuoteId();

    /**
     * Save quote link
     *
     * @return $this
     * @throws Exception
     */
    public function save();

    /**
     * Delete quote link from database
     *
     * @return $this
     * @throws Exception
     */
    public function delete();

    /**
     * Load quote link data
     *
     * @param integer $modelId
     * @param null|string $field
     * @return $this
     */
    public function load($modelId, $field = null);
}