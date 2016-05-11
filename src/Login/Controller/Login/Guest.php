<?php

namespace Amazon\Login\Controller\Login;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Core\Domain\AmazonCustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\SessionFactory;

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
     * @var SessionFactory
     */
    protected $customerSessionFactory;

    /**
     * @param Context $context
     * @param AmazonCustomerFactory $amazonCustomerFactory
     * @param ClientFactoryInterface $clientFactory
     * @param LoggerInterface $logger
     * @param SessionFactory $customerSessionFactory
     */
    public function __construct(
        Context $context,
        AmazonCustomerFactory $amazonCustomerFactory,
        ClientFactoryInterface $clientFactory,
        LoggerInterface $logger,
        SessionFactory $customerSessionFactory
    ) {
        parent::__construct($context);
        $this->amazonCustomerFactory = $amazonCustomerFactory;
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
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
        $this->customerSessionFactory->create()->setAmazonCustomer($amazonCustomer);
    }
}
