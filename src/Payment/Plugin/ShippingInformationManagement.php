<?php

namespace Amazon\Payment\Plugin;

use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Domain\AmazonConstraint;
use Closure;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class ShippingInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var OrderInformationManagementInterface
     */
    protected $orderInformationManagement;

    public function __construct(
        OrderInformationManagementInterface $orderInformationManagement,
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository             = $cartRepository;
        $this->orderInformationManagement = $orderInformationManagement;
    }

    public function aroundSaveAddressInformation(
        ShippingInformationManagementInterface $shippingInformationManagement,
        Closure $proceed,
        $cartId,
        ShippingInformationInterface $shippingInformation
    ) {
        $return = $proceed($cartId, $shippingInformation);

        $quote                  = $this->cartRepository->getActive($cartId);
        $amazonOrderReferenceId = $quote->getExtensionAttributes()->getAmazonOrderReferenceId();

        if ($amazonOrderReferenceId) {
            $this->orderInformationManagement->saveOrderInformation(
                $amazonOrderReferenceId,
                [
                    AmazonConstraint::PAYMENT_PLAN_NOT_SET_ID,
                    AmazonConstraint::PAYMENT_METHOD_NOT_ALLOWED_ID
                ]
            );
        }

        return $return;
    }
}