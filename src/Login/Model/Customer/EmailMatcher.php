<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Customer\EmailMatcherInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class EmailMatcher implements EmailMatcherInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * EmailMatcher constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function match(AmazonCustomer $amazonCustomer)
    {
        try {
            $customerData = $this->customerRepository->get($amazonCustomer->getEmail());

            if ($customerData->getId()) {
                return $customerData;
            }

        } catch (NoSuchEntityException $exception) {
            return null;
        }

        return null;
    }
}