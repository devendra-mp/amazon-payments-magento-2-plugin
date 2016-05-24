<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Fixtures\Order as OrderFixture;
use Magento\Sales\Api\TransactionRepositoryInterface;

class Transaction extends BaseFixture
{
    /**
     * @var TransactionRepositoryInterface
     */
    protected $repository;

    /**
     * @var OrderFixture
     */
    protected $orderFixture;

    public function __construct()
    {
        parent::__construct();
        $this->repository   = $this->getMagentoObject(TransactionRepositoryInterface::class);
        $this->orderFixture = new OrderFixture;
    }

    public function getByTransactionId($transactionId, $paymentId, $orderId)
    {
        return $this->repository->getByTransactionId($transactionId, $paymentId, $orderId);
    }

    public function getByTransactionType($transactionType, $paymentId, $orderId)
    {
        return $this->repository->getByTransactionType($transactionType, $paymentId, $orderId);
    }

    public function getLastTransactionForLastOrder($email)
    {
        $lastOrder = $this->orderFixture->getLastOrderForCustomer($email);

        $transactionId = $lastOrder->getPayment()->getLastTransId();
        $paymentId     = $lastOrder->getPayment()->getId();
        $orderId       = $lastOrder->getId();

        $transaction = $this->getByTransactionId($transactionId, $paymentId, $orderId);

        if ( ! $transaction) {
            throw new \Exception('Last transaction not found for ' . $email);
        }

        return $transaction;
    }
}