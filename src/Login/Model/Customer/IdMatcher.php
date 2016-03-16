<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Data\Customer\IdMatcherInterface;
use Amazon\Login\Api\Data\CustomerLinkInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class IdMatcher implements IdMatcherInterface
{
    /**
     * @var CustomerLinkInterfaceFactory
     */
    protected $customerLinkFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        CustomerLinkInterfaceFactory $customerLinkFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerLinkFactory = $customerLinkFactory;
        $this->customerRepository  = $customerRepository;
    }

    public function match(AmazonCustomer $amazonCustomer)
    {
        $customerLink = $this->customerLinkFactory->create();
        $customerLink->load($amazonCustomer->getId(), 'amazon_id');

        if ($customerLink->getId()) {
            /**
             * @todo: alter to deal with per store customer config, ensure fk constraint
             */
            return $this->customerRepository->getById($customerLink->getCustomerId());
        }

        return null;
    }
}