<?php

namespace Amazon\Payment\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Amazon\Login\Helper\Session;

class LayoutProcessor
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * @param LayoutProcessorinterface $subject
     * @param array $jsLayout
     *
     * @return array
     */
    public function afterProcess(
        LayoutProcessorInterface $subject,
        array $jsLayout
    ) {
        if ($this->session->isAmazonAccountLoggedIn()) {
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']);
        }

        return $jsLayout;
    }
}