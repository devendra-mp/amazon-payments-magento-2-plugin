<?php

namespace Amazon\Payment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OrderLink extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amazon_sales_order', 'entity_id');
    }
}
