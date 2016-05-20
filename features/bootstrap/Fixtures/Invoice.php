<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class Invoice extends BaseFixture
{
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(InvoiceRepositoryInterface::class);
    }

    public function getByTransactionId($transactionId)
    {
        $searchCriteriaBuilder = $this->createMagentoObject(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter(
            'transaction_id', $transactionId
        );

        $searchCriteria = $searchCriteriaBuilder
            ->create();

        $invoices = $this->repository->getList($searchCriteria);

        return current($invoices->getItems());
    }
}