<?php

namespace Amazon\Payment\Api\Data;

interface OrderLinkInterface
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
     * Set order id
     *
     * @param integer $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Get order id
     *
     * @return integer
     */
    public function getOrderId();
}