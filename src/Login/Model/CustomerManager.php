<?php

namespace Amazon\Login\Model;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Data\CustomerLinkInterfaceFactory;
use Amazon\Login\Api\Data\CustomerManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Math\Random;

class CustomerManager implements CustomerManagerInterface
{
    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var Random
     */
    protected $random;

    /**
     * @var CustomerLinkInterfaceFactory
     */
    protected $customerLinkFactory;

    public function __construct(
        CustomerInterfaceFactory $customerDataFactory,
        AccountManagementInterface $accountManagement,
        Random $random,
        CustomerLinkInterfaceFactory $customerLinkFactory
    ) {
        $this->customerDataFactory = $customerDataFactory;
        $this->accountManagement   = $accountManagement;
        $this->random              = $random;
        $this->customerLinkFactory = $customerLinkFactory;
    }

    public function create(AmazonCustomer $amazonCustomer)
    {
        $customerData = $this->customerDataFactory->create();

        $customerData->setFirstname($amazonCustomer->getFirstName());
        $customerData->setLastname($amazonCustomer->getLastName());
        $customerData->setEmail($amazonCustomer->getEmail());
        $password = $this->random->getRandomString(64);

        $customer = $this->accountManagement->createAccount($customerData, $password);

        return $customer;
    }

    public function updateLink($customerId, $amazonId)
    {
        $customerLink = $this->customerLinkFactory
            ->create();

        $customerLink
            ->load($customerId)
            ->setAmazonId($customerId)
            ->setCustomerId($amazonId)
            ->save();
    }
}