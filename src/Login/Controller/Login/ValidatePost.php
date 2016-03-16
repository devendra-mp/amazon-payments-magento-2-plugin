<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Login\Api\Data\CustomerManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\Encryption\Encryptor;

class ValidatePost extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    public function __construct(
        Context $context,
        Session $session,
        AccountRedirect $accountRedirect,
        CustomerRegistry $customerRegistry,
        Encryptor $encryptor,
        CustomerManagerInterface $customerManager,
        CustomerRepository $customerRepository
    )
    {
        parent::__construct($context);

        $this->session = $session;
        $this->accountRedirect = $accountRedirect;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
        $this->customerManager = $customerManager;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        $passwordHash = $this->customerRegistry->retrieveSecureData($this->session->getAmazonMagentoCustomerId())->getPasswordHash();

        /**
         * @todo: handle when customer has no password
         */
        if ($this->encryptor->validateHash($this->getRequest()->getParam('password'), $passwordHash)) {
            $this->customerManager->link($this->session->getAmazonMagentoCustomerId(), $this->session->getAmazonCustomerId());
            $this->loginCustomer($this->customerRepository->getById($this->session->getAmazonMagentoCustomerId()));
            return $this->accountRedirect->getRedirect();
        }

        return $this->_redirect($this->_url->getRouteUrl('*/*/validate'));
    }

    protected function loginCustomer(CustomerInterface $customerData)
    {
        $this->session->setCustomerDataAsLoggedIn($customerData);
        $this->session->regenerateId();
    }
}