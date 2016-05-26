<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Fixtures\Customer as CustomerFixture;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;

class Order extends BaseFixture
{
    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    /**
     * @var OrderRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository      = $this->getMagentoObject(OrderRepositoryInterface::class);
        $this->customerFixture = new CustomerFixture;
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

    public function getLastOrderForCustomer($email)
    {
        $customer = $this->customerFixture->get($email);
        $orders   = $this->getForCustomer($customer);

        $order = current($orders->getItems());

        if ( ! $order) {
            throw new \Exception('Last order not found for ' . $email);
        }

        $order->load($order->getId());

        return $order;
    }
}