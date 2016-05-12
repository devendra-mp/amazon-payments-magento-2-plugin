<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Core\Domain\AmazonCustomerFactory;
use Amazon\Core\Helper\Data as AmazonCoreHelper;
use Amazon\Login\Api\Customer\CompositeMatcherInterface;
use Amazon\Login\Api\CustomerManagerInterface;
use Amazon\Login\Domain\ValidationCredentials;
use Amazon\Login\Helper\Session;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\ValidatorException;
use Psr\Log\LoggerInterface;
use Zend_Validate;

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
     * @var AmazonCustomerFactory
     */
    protected $amazonCustomerFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AmazonCoreHelper
     */
    protected $amazonCoreHelper;

    /**
     * @param Context                   $context
     * @param ClientFactoryInterface    $clientFactory
     * @param CompositeMatcherInterface $matcher
     * @param CustomerManagerInterface  $customerManager
     * @param Session                   $session
     * @param AccountRedirect           $accountRedirect
     * @param AmazonCustomerFactory     $amazonCustomerFactory
     * @param LoggerInterface           $logger
     * @param AmazonCoreHelper          $amazonCoreHelper
     */
    public function __construct(
        Context $context,
        ClientFactoryInterface $clientFactory,
        CompositeMatcherInterface $matcher,
        CustomerManagerInterface $customerManager,
        Session $session,
        AccountRedirect $accountRedirect,
        AmazonCustomerFactory $amazonCustomerFactory,
        LoggerInterface $logger,
        AmazonCoreHelper $amazonCoreHelper
    ) {
        parent::__construct($context);

        $this->clientFactory         = $clientFactory;
        $this->matcher               = $matcher;
        $this->customerManager       = $customerManager;
        $this->session               = $session;
        $this->accountRedirect       = $accountRedirect;
        $this->amazonCustomerFactory = $amazonCustomerFactory;
        $this->logger                = $logger;
        $this->amazonCoreHelper      = $amazonCoreHelper;
    }

    public function execute()
    {
        if ( ! $this->amazonCoreHelper->isLwaEnabled()) {
            throw new NotFoundException(__('Action is not available'));
        }

        try {
            $userInfo = $this->clientFactory->create()->getUserInfo($this->getRequest()->getParam('access_token'));

            if (is_array($userInfo) && isset($userInfo['user_id'])) {
                $amazonCustomer = $this->amazonCustomerFactory->create([
                    'id'    => $userInfo['user_id'],
                    'email' => $userInfo['email'],
                    'name'  => $userInfo['name'],
                ]);

                $processed = $this->processAmazonCustomer($amazonCustomer);

                if ($processed instanceof ValidationCredentials) {
                    $this->session->setValidationCredentials($processed);
                    return $this->_redirect($this->_url->getRouteUrl('*/*/validate'));
                } else {
                    $this->session->login($processed);
                }
            }

        } catch (ValidatorException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addError(__('Error processing Amazon Login'));
        }

        return $this->accountRedirect->getRedirect();
    }

    protected function processAmazonCustomer(AmazonCustomer $amazonCustomer)
    {
        $customerData = $this->matcher->match($amazonCustomer);

        if (null === $customerData) {
            return $this->createCustomer($amazonCustomer);
        }

        if ($amazonCustomer->getId() != $customerData->getExtensionAttributes()->getAmazonId()) {
            if ( ! $this->session->isLoggedIn()) {
                return new ValidationCredentials($customerData->getId(), $amazonCustomer->getId());
            }

            $this->customerManager->updateLink($customerData->getId(), $amazonCustomer->getId());
        }

        return $customerData;
    }

    protected function createCustomer(AmazonCustomer $amazonCustomer)
    {
        if ( ! Zend_Validate::is($amazonCustomer->getEmail(), 'EmailAddress')) {
            throw new ValidatorException(__('the email address for your Amazon account is invalid'));
        }

        $customerData = $this->customerManager->create($amazonCustomer);
        $this->customerManager->updateLink($customerData->getId(), $amazonCustomer->getId());

        return $customerData;
    }
}
