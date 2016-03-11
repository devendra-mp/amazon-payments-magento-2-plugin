<?php

namespace Amazon\Login\Plugin;

use Amazon\Login\Api\Data\CustomerInterfaceFactory as AmazonCustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepository
{
    /**
     * @var CustomerExtensionFactory
     */
    private $customerExtensionFactory;

    /**
     * @var AmazonCustomerFactory
     */
    private $amazonCustomerFactory;

    public function __construct(
        CustomerExtensionFactory $customerExtensionFactory,
        AmazonCustomerFactory $amazonCustomerFactory
    ) {
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->amazonCustomerFactory = $amazonCustomerFactory;
    }

    public function afterGetById(CustomerRepositoryInterface $customerRepository, CustomerInterface $customer)
    {
        $customerExtension = ($customer->getExtensionAttributes()) ?: $this->customerExtensionFactory->create();

        $amazonCustomer = $this->amazonCustomerFactory->create();
        $amazonCustomer->load($customer->getId(), 'customer_id');

        if ($amazonCustomer->getId()) {
            $customerExtension->setAmazonId($amazonCustomer->getAmazonId());
        }

        $customer->setExtensionAttributes($customerExtension);

        return $customer;
    }
}