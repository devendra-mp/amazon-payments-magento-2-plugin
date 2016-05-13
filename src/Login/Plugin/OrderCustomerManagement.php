<?php

namespace Amazon\Login\Plugin;

use Amazon\Login\Api\CustomerManagerInterface;
use Amazon\Login\Helper\Session as LoginSessionHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Sales\Api\OrderCustomerManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderCustomerManagement
{
    /**
     * @var LoginSessionHelper
     */
    protected $loginSessionHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var CustomerManagerInterface
     */
    protected $customerManager;

    /**
     * @param LoginSessionHelper $loginSessionHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerManagerInterface $customerManager
     */
    public function __construct(
        LoginSessionHelper $loginSessionHelper,
        OrderRepositoryInterface $orderRepository,
        CustomerManagerInterface $customerManager
    ) {
        $this->loginSessionHelper = $loginSessionHelper;
        $this->orderRepository = $orderRepository;
        $this->customerManager = $customerManager;
    }

    /**
     * @param OrderCustomerManagementInterface $subject
     * @param \Closure $proceed
     * @param int $orderId
     * @return CustomerInterface
     */
    public function aroundCreate(OrderCustomerManagementInterface $subject, \Closure $proceed, $orderId)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customerData */
        $customerData = $proceed($orderId);
        $isAmazonPayment = $this->orderRepository->get($orderId)->getPayment()->getMethod() === 'amazon_payment';
        $amazonCustomer = $this->loginSessionHelper->getAmazonCustomer();

        if ($isAmazonPayment && $amazonCustomer) {
            $this->customerManager->updateLink($customerData->getId(), $amazonCustomer->getId());
        }

        return $customerData;
    }
}
