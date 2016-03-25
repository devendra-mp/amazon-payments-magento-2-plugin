<?php

namespace Amazon\Login\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerLink extends AbstractDb
{
    const TABLE_NAME = 'amazon_customer';

    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, 'entity_id');
    }
}
