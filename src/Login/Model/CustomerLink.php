<?php

namespace Amazon\Login\Model;

use Amazon\Login\Api\Data\CustomerLinkInterface;
use Amazon\Login\Model\ResourceModel\CustomerLink as CustomerLinkResourceModel;
use Magento\Framework\Model\AbstractModel;

class CustomerLink extends AbstractModel implements CustomerLinkInterface
{
    protected function _construct()
    {
        $this->_init(CustomerLinkResourceModel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setAmazonId($amazonId)
    {
        return $this->setData('amazon_id', $amazonId);
    }

    /**
     * {@inheritDoc}
     */
    public function getAmazonId()
    {
        return $this->getData('amazon_id');
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }
}
