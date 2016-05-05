<?php

namespace Amazon\Payment\Model\ResourceModel\PendingCapture;

use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amazon\Payment\Model\PendingCapture as PendingCaptureModel;
use Amazon\Payment\Model\ResourceModel\PendingCapture as PendingCaptureResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(PendingCaptureModel::class, PendingCaptureResourceModel::class);
    }

    public function getIdGenerator()
    {
        $this->_renderFilters()->_renderOrders()->_renderLimit();
        $select = $this->getSelect();

        $statement = $select->getConnection()->query($select, $this->_bindParams);

        while ($row = $statement->fetch()) {
            yield $row['authorization_id'];
        }
    }
}