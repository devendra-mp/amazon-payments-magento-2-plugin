<?php

namespace Amazon\Payment\Api\Data;

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
}