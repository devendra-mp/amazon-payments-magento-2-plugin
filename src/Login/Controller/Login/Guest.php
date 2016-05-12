<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Core\Domain\AmazonCustomerFactory;
use Amazon\Core\Helper\Data as AmazonCoreHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

class Guest extends Action
{
    /**
     * @var AmazonCustomerFactory
     */
    protected $amazonCustomerFactory;

    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var AmazonCoreHelper
     */
    protected $amazonCoreHelper;

    /**
     * @param Context $context
     * @param AmazonCustomerFactory $amazonCustomerFactory
     * @param ClientFactoryInterface $clientFactory
     * @param LoggerInterface $logger
     * @param Session $customerSession
     * @param AmazonCoreHelper $amazonCoreHelper
     */
    public function __construct(
        Context $context,
        AmazonCustomerFactory $amazonCustomerFactory,
        ClientFactoryInterface $clientFactory,
        LoggerInterface $logger,
        Session $customerSession,
        AmazonCoreHelper $amazonCoreHelper
    ) {
        parent::__construct($context);
        $this->amazonCustomerFactory = $amazonCustomerFactory;
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->amazonCoreHelper = $amazonCoreHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->amazonCoreHelper->isLwaEnabled()) {
            throw new NotFoundException(__('Action is not available'));
        }

        try {
            $userInfo = $this->clientFactory
                             ->create()
                             ->getUserInfo($this->getRequest()->getParam('access_token'));

            if (is_array($userInfo) && isset($userInfo['user_id'])) {
                $amazonCustomer = $this->amazonCustomerFactory->create([
                    'id'    => $userInfo['user_id'],
                    'email' => $userInfo['email'],
                    'name'  => $userInfo['name'],
                ]);

                $this->storeUserInfoToSession($amazonCustomer);
            }

        } catch (\Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addError(__('Error processing Amazon Login'));
        }

        return $this->resultRedirectFactory->create()->setPath('checkout/cart');
    }

    /**
     * @param AmazonCustomer $amazonCustomer
     * @return void
     */
    protected function storeUserInfoToSession(AmazonCustomer $amazonCustomer)
    {
        $this->customerSession->setAmazonCustomer($amazonCustomer);
    }
}
