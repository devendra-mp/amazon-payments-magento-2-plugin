<?php

namespace Amazon\Payment\Api\Data;

use Exception;

interface QuoteLinkInterface
{
    /**
     * @return mixed
     */
    public function getId();

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
     * Set sandbox simulation reference
     *
     * @param string $sandboxSimulationReference
     *
     * @return $this
     */
    public function setSandboxSimulationReference($sandboxSimulationReference);

    /**
     * Get sandbox simulation reference
     *
     * @return string
     */
    public function getSandboxSimulationReference();

    /**
     * Set quote confirmed with amazon
     *
     * @param boolean $confirmed
     *
     * @return $this
     */
    public function setConfirmed($confirmed);


    /**
     * Get quote confirmed with amazon
     *
     * @return boolean
     */
    public function isConfirmed();

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
