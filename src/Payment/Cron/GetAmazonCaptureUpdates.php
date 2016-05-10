<?php

namespace Amazon\Payment\Cron;

use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Model\ResourceModel\PendingCapture\CollectionFactory;
use Magento\Framework\Data\Collection;

class GetAmazonCaptureUpdates
{
    /**
     * @var int
     */
    protected $limit = 100;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var PaymentManagementInterface
     */
    protected $paymentManagement;

    public function __construct(
        CollectionFactory $collectionFactory,
        PaymentManagementInterface $paymentManagement
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->paymentManagement = $paymentManagement;
    }

    public function execute()
    {
        $collection = $this->collectionFactory
            ->create()
            ->addOrder(PendingCaptureInterface::CREATED_AT, Collection::SORT_ORDER_ASC)
            ->setPageSize($this->limit)
            ->setCurPage(1);

        $pendingCaptureIds = $collection->getIdGenerator();
        foreach($pendingCaptureIds as $pendingCaptureId) {
            $this->paymentManagement->updateCapture($pendingCaptureId);
        }
    }
}