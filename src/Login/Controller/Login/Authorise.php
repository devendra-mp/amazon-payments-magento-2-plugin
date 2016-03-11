<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Login\Api\Data\CustomerInterfaceFactory as AmazonCustomerFactory;
use Amazon\Core\Domain\Name;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerExtensionFactory;
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

    public function __construct(
        Context $context,
        ClientFactoryInterface $clientFactory,
        AccountManagementInterface $accountManagement,
        Session $customerSession,
        CustomerInterfaceFactory $customerDataFactory,
        AccountRedirect $accountRedirect,
        Random $random,
        CustomerExtensionFactory $customerExtensionFactory,
        AmazonCustomerFactory $amazonCustomerFactory
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
    }

    public function execute()
    {
        $userInfo = $this->client->getUserInfo($this->getRequest()->getParam('access_token'));

        if (is_array($userInfo)) {
            if (isset($userInfo['user_id'])) {
                $customerData = $this->customerDataFactory->create();

                $name = new Name($userInfo['name']);
                $customerData->setFirstname($name->getFirstName());
                $customerData->setLastname($name->getLastName());
                $customerData->setEmail($userInfo['email']);
                $password = $this->random->getRandomString(64);

                $customer = $this->accountManagement->createAccount($customerData, $password);

                $amazonCustomer = $this->amazonCustomerFactory->create();

                $amazonCustomer
                    ->setAmazonId($userInfo['user_id'])
                    ->setCustomerId($customer->getId())
                    ->save();

                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $this->customerSession->regenerateId();
            }
        }

        return $this->accountRedirect->getRedirect();
    }
}