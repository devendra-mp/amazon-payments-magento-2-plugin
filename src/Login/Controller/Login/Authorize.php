<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Customer\CompositeMatcherInterface;
use Amazon\Login\Api\CustomerManagerInterface;
use Amazon\Login\Domain\ValidationCredentials;
use Amazon\Login\Helper\Session;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Authorize extends Action
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

    /**
     * Authorize constructor.
     *
     * @param Context                   $context
     * @param ClientFactoryInterface    $clientFactory
     * @param CompositeMatcherInterface $matcher
     * @param CustomerManagerInterface  $customerManager
     * @param Session                   $session
     * @param AccountRedirect           $accountRedirect
     */
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

            $processed = $this->processAmazonCustomer($amazonCustomer);

            if ($processed instanceof ValidationCredentials) {
                $this->session->setValidationCredentials($processed);
                return $this->_redirect($this->_url->getRouteUrl('*/*/validate'));
            } else {
                $this->session->login($processed);
            }
        }

        return $this->accountRedirect->getRedirect();
    }

    protected function processAmazonCustomer(AmazonCustomer $amazonCustomer)
    {
        $customerData = $this->matcher->match($amazonCustomer);

        if (null === $customerData) {
            return $this->createCustomer($amazonCustomer);
        }

        if (null === $customerData->getExtensionAttributes()->getAmazonId()) {
            return new ValidationCredentials($customerData->getId(), $amazonCustomer->getId());
        }

        return $customerData;
    }

    protected function createCustomer(AmazonCustomer $amazonCustomer)
    {
        $customerData = $this->customerManager->create($amazonCustomer);
        $this->customerManager->updateLink($customerData->getId(), $amazonCustomer->getId());

        return $customerData;
    }
}