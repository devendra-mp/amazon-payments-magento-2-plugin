<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Sales\Api\TransactionRepositoryInterface;

class Transaction extends BaseFixture
{
    /**
     * @var TransactionRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(TransactionRepositoryInterface::class);
    }

    public function getByTransactionId($transactionId, $paymentId, $orderId)
    {
        return $this->repository->getByTransactionId($transactionId, $paymentId, $orderId);
    }
}