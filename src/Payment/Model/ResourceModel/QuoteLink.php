<?php

namespace Amazon\Payment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class QuoteLink extends AbstractDb
{
    const TABLE_NAME = 'amazon_quote';
    
    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, 'entity_id');
    }
}
