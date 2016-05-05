<?php

namespace Amazon\Payment\Model\ResourceModel;

use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PendingCapture extends AbstractDb
{
    const TABLE_NAME = 'amazon_pending_capture';

    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, PendingCaptureInterface::ID);
    }

    /**
     * {@inheritDoc}
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->forUpdate($object->getLockOnLoad());

        return $select;
    }
}
