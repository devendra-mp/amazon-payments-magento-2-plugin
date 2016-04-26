<?php

namespace Amazon\Payment\Plugin;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Payment\Api\OrderInformationManagementInterface;
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
            $saveOrderInformation = $this->orderInformationManagement->saveOrderInformation($amazonOrderReferenceId);

            if ( ! $saveOrderInformation) {
                throw new AmazonServiceUnavailableException();
            }
        }

        return $return;
    }
}