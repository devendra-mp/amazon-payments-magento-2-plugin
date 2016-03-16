<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Login\Api\Data\CustomerManagerInterface;
use Amazon\Login\Domain\ValidationCredentials;
use Amazon\Login\Helper\Session;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
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
    ) {
        parent::__construct($context);

        $this->session            = $session;
        $this->accountRedirect    = $accountRedirect;
        $this->customerRegistry   = $customerRegistry;
        $this->encryptor          = $encryptor;
        $this->customerManager    = $customerManager;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        $credentials = $this->session->getValidationCredentials();

        if ($credentials instanceof ValidationCredentials) {
            $password = $this->getRequest()->getParam('password');
            $hash     = $this->customerRegistry->retrieveSecureData($credentials->getCustomerId())->getPasswordHash();

            if ($this->encryptor->validateHash($password, $hash)) {
                $this->customerManager->link($credentials->getCustomerId(), $credentials->getAmazonId());
                $this->session->login($this->customerRepository->getById($credentials->getCustomerId()));
            } else {
                return $this->_redirect($this->_url->getRouteUrl('*/*/validate'));
            }
        }

        return $this->accountRedirect->getRedirect();
    }
}