<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Data\Customer\IdMatcherInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class IdMatcher implements IdMatcherInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function match(AmazonCustomer $amazonCustomer)
    {
        $this->searchCriteriaBuilder->addFilter(
            'amazon_id', $amazonCustomer->getId()
        );

        $searchCriteria = $this->searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();

        $customerList = $this->customerRepository->getList($searchCriteria);

        if (count($items = $customerList->getItems())) {
            return current($items);
        }

        return null;
    }
}