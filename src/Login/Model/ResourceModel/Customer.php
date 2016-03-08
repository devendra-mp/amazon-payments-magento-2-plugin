<?php

namespace Amazon\Login\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Customer extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amazon_customer', 'customer_id');
    }
}
