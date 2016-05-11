<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Checkout extends Page
{
    use PageTrait;

    protected $path = '/checkout/';

    protected $elements
        = [
            'shipping-widget'       => '#OffAmazonPaymentsWidgets0IFrame',
            'payment-widget'        => '#OffAmazonPaymentsWidgets1IFrame',
            'first-amazon-address'  => ['css' => '.address-list li:nth-of-type(1) a'],
            'first-amazon-payment'  => ['css' => '.payment-list li:nth-of-type(1) a'],
            'go-to-billing'         => ['css' => 'button.continue.primary'],
            'first-shipping-method' => ['css' => 'input[name="shipping_method"]:nth-of-type(1)'],
            'billing-address'       => ['css' => '.amazon-billing-address'],
            'full-screen-loader'    => ['css' => '.loading-mask'],
            'shipping-loader'       => ['css' => '.checkout-shipping-method._block-content-loading'],
            'revert-checkout'       => ['css' => '.revert-checkout'],
            'shipping-form'         => ['css' => '#co-shipping-form']
        ];

    public function selectFirstAmazonShippingAddress()
    {
        $this->waitForElement('shipping-widget');
        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets0IFrame');
        $this->clickElement('first-amazon-address');
        $this->getDriver()->switchToIFrame(null);
    }

    public function selectFirstAmazonPaymentMethod()
    {
        $this->waitForElement('payment-widget');
        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets1IFrame');
        $this->clickElement('first-amazon-payment');
        $this->getDriver()->switchToIFrame(null);
    }

    public function selectDefaultShippingMethod()
    {
        $this->waitUntilElementDisappear('shipping-loader');

        $defaultShippingMethod = $this->getElementWithWait('first-shipping-method');
        if ( ! $defaultShippingMethod->isChecked()) {
            $defaultShippingMethod->click();
        }
    }

    public function goToBilling()
    {
        $this->clickElement('go-to-billing');
        $this->waitUntilElementDisappear('full-screen-loader');
    }

    public function getBillingAddress()
    {
        $this->waitUntilElementDisappear('full-screen-loader');

        return $this->getElementText('billing-address');
    }

    public function revertToStandardCheckout()
    {
        $this->clickElement('revert-checkout');
    }

    public function hasStandardShippingForm()
    {
        try {
            $element = $this->getElementWithWait('shipping-form');
            return $element->isVisible();
        } catch (\Exception $e) {
            return false;
        }
    }
}