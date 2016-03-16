<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Data\Customer\CompositeMatcherInterface;
use Amazon\Login\Api\Data\CustomerManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Authorise extends Action
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var CompositeMatcherInterface
     */
    protected $matcher;

    /**
     * @var CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    public function __construct(
        Context $context,
        ClientFactoryInterface $clientFactory,
        CompositeMatcherInterface $matcher,
        CustomerManagerInterface $customerManager,
        Session $session,
        AccountRedirect $accountRedirect
    ) {
        parent::__construct($context);

        $this->clientFactory   = $clientFactory;
        $this->matcher         = $matcher;
        $this->customerManager = $customerManager;
        $this->session         = $session;
        $this->accountRedirect = $accountRedirect;
    }

    public function execute()
    {
        $userInfo = $this->clientFactory->create()->getUserInfo($this->getRequest()->getParam('access_token'));

        if (is_array($userInfo) && isset($userInfo['user_id'])) {

            $amazonCustomer = new AmazonCustomer($userInfo['user_id'], $userInfo['email'], $userInfo['name']);

            $customerData = $this->matcher->match($amazonCustomer);

            if (null === $customerData) {
                $customerData = $this->customerManager->create($amazonCustomer);
                $this->customerManager->link($customerData->getId(), $amazonCustomer->getId());
            } else if (null === $customerData->getExtensionAttributes()->getAmazonId()) {
                $this->session->setAmazonCustomerId($amazonCustomer->getId());
                $this->session->setAmazonMagentoCustomerId($customerData->getId());
                return $this->_redirect($this->_url->getRouteUrl('*/*/validate'));
            }

            $this->loginCustomer($customerData);
        }

        return $this->accountRedirect->getRedirect();
    }

    protected function loginCustomer(CustomerInterface $customerData)
    {
        $this->session->setCustomerDataAsLoggedIn($customerData);
        $this->session->regenerateId();
    }
}