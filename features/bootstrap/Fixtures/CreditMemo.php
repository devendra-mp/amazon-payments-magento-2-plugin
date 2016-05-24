<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\CreditmemoRepositoryInterface;

class CreditMemo extends BaseFixture
{
    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(CreditmemoRepositoryInterface::class);
    }

    public function getLastForOrder($orderid)
    {
        $searchCriteriaBuilder = $this->createMagentoObject(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter(
            'order_id', $orderid
        );

        $searchCriteriaBuilder->addSortOrder(
            'created_at', 'DESC'
        );

        $searchCriteria = $searchCriteriaBuilder
            ->create();

        $creditMemos = $this->repository->getList($searchCriteria);

        $creditMemo = current($creditMemos->getItems());

        if ( ! $creditMemo) {
            throw new \Exception('Credit memo not found for order id ' . $orderid);
        }

        return $creditMemo;
    }
}