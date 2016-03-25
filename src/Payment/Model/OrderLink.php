<?php

namespace Amazon\Payment\Model;

use Amazon\Payment\Api\Data\OrderLinkInterface;
use Amazon\Payment\Model\ResourceModel\OrderLink as OrderLinkResourceModel;
use Magento\Framework\Model\AbstractModel;

class OrderLink extends AbstractModel implements OrderLinkInterface
{
    protected function _construct()
    {
        $this->_init(OrderLinkResourceModel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setAmazonOrderReferenceId($amazonOrderReferenceId)
    {
        return $this->setData('amazon_order_reference_id', $amazonOrderReferenceId);
    }

    /**
     * {@inheritDoc}
     */
    public function getAmazonOrderReferenceId()
    {
        return $this->getData('amazon_order_reference_id');
    }

    /**
     * {@inheritDoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }
}