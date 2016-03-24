<?php

namespace Amazon\Payment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class QuoteLink extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amazon_quote', 'entity_id');
    }
}
