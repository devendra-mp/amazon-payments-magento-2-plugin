<?php

namespace Amazon\Login\Helper;

use Amazon\Login\Domain\ValidationCredentials;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Session
{
    /**
     * @var CustomerSession
     */
    protected $session;

    /**
     * Session constructor.
     *
     * @param CustomerSession $session
     */
    public function __construct(
        CustomerSession $session
    ) {
        $this->session = $session;
    }

    /**
     * Login customer by data
     *
     * @param CustomerInterface $customerData
     */
    public function login(CustomerInterface $customerData)
    {
        $this->session->setCustomerDataAsLoggedIn($customerData);
        $this->session->regenerateId();
    }

    /**
     * Login customer by id
     *
     * @param integer $customerId
     */
    public function loginById($customerId)
    {
        $this->session->loginById($customerId);
        $this->session->regenerateId();
    }

    /**
     * Set validation credentials in session
     *
     * @param ValidationCredentials $credentials
     */
    public function setValidationCredentials(ValidationCredentials $credentials)
    {
        $this->session->setAmazonValidationCredentials($credentials);
    }

    /**
     * Get validation credentials from session
     *
     * @return ValidationCredentials|null
     */
    public function getValidationCredentials()
    {
        $credentials = $this->session->getAmazonValidationCredentials();

        return ($credentials) ?: null;
    }
}