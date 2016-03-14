<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Data\Customer\EmailMatcherInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class EmailMatcher implements EmailMatcherInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function match(AmazonCustomer $amazonCustomer)
    {
        $customerData = $this->customerRepository->get($amazonCustomer->getEmail());

        if ($customerData->getId()) {
            return $customerData;
        }

        return null;
    }
}