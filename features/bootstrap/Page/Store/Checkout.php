<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Checkout extends Page
{
    use PageTrait;

    protected $path = '/checkout';

    protected $elements
        = [
            'shipping-widget'         => '#OffAmazonPaymentsWidgets0IFrame',
            'payment-widget'          => '#OffAmazonPaymentsWidgets1IFrame',
            'first-amazon-address'    => ['css' => '.address-list-container a:nth-of-type(1)'],
            'first-amazon-payment'    => ['css' => '.payment-list-container a:nth-of-type(1)'],
            'go-to-billing'           => ['css' => 'button.continue'],
            'block-loader'            => ['css' => '._block-content-loading'],
            'body-loader'             => ['css' => '.loading-mask'],
            'default-shipping-method' => '#s_method_flatrate',
            'billing-address'         => ['css' => '.amazon-billing-address']
        ];

    public function selectFirstAmazonShippingAddress()
    {
        $this->waitForElement('shipping-widget');

        $currentWindow = $this->getDriver()->getWindowName();
        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets0IFrame');

        $this->clickElement('first-amazon-address');

        $this->getDriver()->switchToWindow($currentWindow);
    }

    public function selectFirstAmazonPaymentMethod()
    {
        $this->waitForElement('payment-widget');

        $currentWindow = $this->getDriver()->getWindowName();
        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets1IFrame');

        $this->clickElement('first-amazon-payment');

        $this->getDriver()->switchToWindow($currentWindow);
    }

    public function selectDefaultShippingMethod()
    {
        $this->waitUntilElementDisappear('block-loader');
        $this->waitForElement('default-shipping-method');
    }

    public function goToBilling()
    {
        $this->waitUntilElementDisappear('block-loader');
        $this->clickElement('go-to-billing');
        $this->waitUntilElementDisappear('body-loader');
    }

    public function getBillingAddress()
    {
        $this->getElementText('billing-address');
    }
}