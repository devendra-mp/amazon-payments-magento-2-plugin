<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;

class Order extends BaseFixture
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(OrderRepositoryInterface::class);
    }

    public function getForCustomer(CustomerInterface $customer)
    {
        $searchCriteriaBuilder = $this->createMagentoObject(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter(
            'customer_id', $customer->getId()
        );
        
        $searchCriteriaBuilder->addSortOrder(
            'created_at', 'DESC'
        );

        $searchCriteria = $searchCriteriaBuilder
            ->create();

        return $this->repository->getList($searchCriteria);
    }
}