<?php

namespace Amazon\Login\Plugin;

use Amazon\Login\Api\Data\CustomerLinkInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepository
{
    /**
     * @var CustomerExtensionFactory
     */
    protected $customerExtensionFactory;

    /**
     * @var CustomerLinkInterfaceFactory
     */
    protected $customerLinkFactory;

    /**
     * CustomerRepository constructor.
     *
     * @param CustomerExtensionFactory     $customerExtensionFactory
     * @param CustomerLinkInterfaceFactory $customerLinkFactory
     */
    public function __construct(
        CustomerExtensionFactory $customerExtensionFactory,
        CustomerLinkInterfaceFactory $customerLinkFactory
    ) {
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->customerLinkFactory      = $customerLinkFactory;
    }

    /**
     * Add amazon id extension attribute to customer
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface           $customer
     *
     * @return CustomerInterface
     */
    public function afterGetById(CustomerRepositoryInterface $customerRepository, CustomerInterface $customer)
    {
        $this->setAmazonIdExtensionAttribute($customer);

        return $customer;
    }

    /**
     * Add amazon id extension attribute to customer
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface           $customer
     *
     * @return CustomerInterface
     */
    public function afterGet(CustomerRepositoryInterface $customerRepository, CustomerInterface $customer)
    {
        $this->setAmazonIdExtensionAttribute($customer);

        return $customer;
    }

    protected function setAmazonIdExtensionAttribute(CustomerInterface $customer)
    {
        $customerExtension = ($customer->getExtensionAttributes()) ?: $this->customerExtensionFactory->create();

        $amazonCustomer = $this->customerLinkFactory->create();
        $amazonCustomer->load($customer->getId(), 'customer_id');

        if ($amazonCustomer->getId()) {
            $customerExtension->setAmazonId($amazonCustomer->getAmazonId());
        }

        $customer->setExtensionAttributes($customerExtension);
    }
}