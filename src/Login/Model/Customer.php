<?php

namespace Amazon\Login\Model;

use Amazon\Login\Api\Data\CustomerInterface;
use Amazon\Login\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Framework\Model\AbstractModel;

class Customer extends AbstractModel implements CustomerInterface
{
    protected function _construct()
    {
        $this->_init(CustomerResourceModel::class);
    }

    public function setAmazonId($amazonId)
    {
        return $this->setData('amazon_id', $amazonId);
    }

    public function getAmazonId()
    {
        return $this->getData('amazon_id');
    }

    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }
}
