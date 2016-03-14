<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Login\Api\Data\CustomerInterfaceFactory as AmazonCustomerFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Math\Random;
use PayWithAmazon\ClientInterface;

class Authorise extends Action
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var AccountRedirect
     */
    private $accountRedirect;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var CustomerExtensionFactory
     */
    private $customerExtensionFactory;

    /**
     * @var AmazonCustomerFactory
     */
    private $amazonCustomerFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        Context $context,
        ClientFactoryInterface $clientFactory,
        AccountManagementInterface $accountManagement,
        Session $customerSession,
        CustomerInterfaceFactory $customerDataFactory,
        AccountRedirect $accountRedirect,
        Random $random,
        CustomerExtensionFactory $customerExtensionFactory,
        AmazonCustomerFactory $amazonCustomerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);

        $this->client                   = $clientFactory->create();
        $this->accountManagement        = $accountManagement;
        $this->customerDataFactory      = $customerDataFactory;
        $this->customerSession          = $customerSession;
        $this->accountRedirect          = $accountRedirect;
        $this->random                   = $random;
        $this->customerExtensionFactory = $customerExtensionFactory;
        $this->amazonCustomerFactory    = $amazonCustomerFactory;
        $this->customerRepository       = $customerRepository;
    }

    public function execute()
    {
        $userInfo = $this->client->getUserInfo($this->getRequest()->getParam('access_token'));

        if (is_array($userInfo)) {
            if (isset($userInfo['user_id'])) {
                $amazonCustomer = $this->amazonCustomerFactory->create();
                $amazonCustomer->load($userInfo['user_id'], 'amazon_id');

                if ($amazonCustomer->getId()) {
                    $customer = $this->customerRepository->getById($amazonCustomer->getCustomerId());
                    $this->loginCustomer($customer);
                } else {
                    $customer = $this->createCustomer($userInfo);
                    $this->loginCustomer($customer);
                }
            }
        }

        return $this->accountRedirect->getRedirect();
    }

    private function createCustomer($amazonData)
    {
        $customerData = $this->customerDataFactory->create();

        $name = new Name($amazonData['name']);
        $customerData->setFirstname($name->getFirstName());
        $customerData->setLastname($name->getLastName());
        $customerData->setEmail($amazonData['email']);
        $password = $this->random->getRandomString(64);

        $customer = $this->accountManagement->createAccount($customerData, $password);

        $amazonCustomer = $this->amazonCustomerFactory->create();

        $amazonCustomer
            ->setAmazonId($amazonData['user_id'])
            ->setCustomerId($customer->getId())
            ->save();

        return $customer;
    }

    private function loginCustomer(CustomerInterface $customerData)
    {
        $this->customerSession->setCustomerDataAsLoggedIn($customerData);
        $this->customerSession->regenerateId();
    }
}