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
            'shipping-widget'       => '#OffAmazonPaymentsWidgets0IFrame',
            'payment-widget'        => '#OffAmazonPaymentsWidgets1IFrame',
            'first-amazon-address'  => ['css' => '.address-list li:nth-of-type(1) a'],
            'first-amazon-payment'  => ['css' => '.payment-list li:nth-of-type(1) a'],
            'go-to-billing'         => ['css' => 'button.continue.primary'],
            'first-shipping-method' => ['css' => 'input[name="shipping_method"]:nth-of-type(1)'],
            'billing-address'       => ['css' => '.amazon-billing-address'],
            'full-screen-loader'    => ['css' => '.loading-mask']
        ];

    public function selectFirstAmazonShippingAddress()
    {
        $this->waitForElement('shipping-widget');

        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets0IFrame');

        $this->clickElement('first-amazon-address');

        $this->getDriver()->switchToIFrame(null);

        $this->waitForAjaxRequestsToComplete();
    }

    public function selectFirstAmazonPaymentMethod()
    {
        $this->waitForCondition('1 === 2', 30000);

        $this->waitForElement('payment-widget');

        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets1IFrame');

        $this->clickElement('first-amazon-payment');

        $this->getDriver()->switchToIFrame(null);

        $this->waitForAjaxRequestsToComplete();
    }

    public function selectDefaultShippingMethod()
    {
        $defaultShippingMethod = $this->getElementWithWait('first-shipping-method');

        if ( ! $defaultShippingMethod->isChecked()) {
            $defaultShippingMethod->click();
        }
    }

    public function goToBilling()
    {
        $this->waitForCondition('1 === 2', 30000);
        $this->waitForAjaxRequestsToComplete();
        
        $this->clickElement('go-to-billing');
    }

    public function getBillingAddress()
    {
        return $this->getElementText('billing-address');
    }
}