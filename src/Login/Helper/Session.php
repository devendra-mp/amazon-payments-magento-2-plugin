<?php

namespace Amazon\Login\Helper;

use Amazon\Login\Domain\ValidationCredentials;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class Session
{
    /**
     * @var CustomerSession
     */
    protected $session;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Session constructor.
     *
     * @param CustomerSession       $session
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        CustomerSession $session,
        EventManagerInterface $eventManager
    ) {
        $this->session      = $session;
        $this->eventManager = $eventManager;
    }

    /**
     * Login customer by data
     *
     * @param CustomerInterface $customerData
     */
    public function login(CustomerInterface $customerData)
    {
        $this->dispatchAuthenticationEvent();
        $this->session->setCustomerDataAsLoggedIn($customerData);
        $this->session->regenerateId();
        $this->setAmazonAccountLoggedIn();
    }

    /**
     * Login customer by id
     *
     * @param integer $customerId
     */
    public function loginById($customerId)
    {
        $this->dispatchAuthenticationEvent();
        $this->session->loginById($customerId);
        $this->session->regenerateId();
        $this->setAmazonAccountLoggedIn();
    }

    /**
     * For compatibility with customer_customer_authenticated event dispatched from standard login controller.
     * The observers are also attached to this with the exception being password related ones which are omitted.
     */
    protected function dispatchAuthenticationEvent()
    {
        $this->eventManager->dispatch('amazon_customer_authenticated');
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

    /**
     * Set flag to indicate that amazon account is logged in
     *
     * @return null
     */
    public function setAmazonAccountLoggedIn()
    {
        $this->session->setAmazonAccountLoggedIn(true);
        $this->eventManager->dispatch('amazon_account_login');
    }

    /**
     * Set flag to indicate that amazon account is logged out
     *
     * @return null
     */
    public function setAmazonAccountLoggedOut()
    {
        $this->session->setAmazonAccountLoggedIn(false);
        $this->eventManager->dispatch('amazon_account_logout');
    }

    /**
     * Get flag to find if amazon account is logged in
     *
     * @return bool
     */
    public function isAmazonAccountLoggedIn()
    {
        return (bool)$this->session->getAmazonAccountLoggedIn();
    }
}