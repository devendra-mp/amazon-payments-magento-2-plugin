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
    private $session;

    public function __construct(
        CustomerSession $session
    ) {
        $this->session = $session;
    }

    public function login(CustomerInterface $customerData)
    {
        $this->session->setCustomerDataAsLoggedIn($customerData);
        $this->session->regenerateId();
    }

    /**
     * @param integer $customerId
     * @param integer $amazonId
     */
    public function setValidationCredentials(ValidationCredentials $credentials)
    {
        $this->session->setAmazonValidationCredentials($credentials);
    }

    /**
     * @return ValidationCredentials|null
     */
    public function getValidationCredentials()
    {
        $credentials = $this->session->getAmazonValidationCredentials();

        if (!$credentials) {
            return null;
        }

        return $credentials;
    }
}