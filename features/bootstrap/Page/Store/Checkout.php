<?php

namespace Page\Store;

use Page\PageTrait;
use Page\Store\Element\Checkout\PaymentMethodForm;
use Page\Store\Element\Checkout\ShippingAddressForm;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Checkout extends Page
{
    use PageTrait;

    protected $path = '/checkout/';

    protected $elements
        = [
            'shipping-widget'            => ['css' => '#OffAmazonPaymentsWidgets0IFrame'],
            'payment-widget'             => ['css' => '#OffAmazonPaymentsWidgets1IFrame'],
            'alternative-payment-widget' => ['css' => '#OffAmazonPaymentsWidgets2IFrame'],
            'first-amazon-address'       => ['css' => '.address-list li:nth-of-type(1) a'],
            'first-amazon-payment'       => ['css' => '.payment-list li:nth-of-type(1) a'],
            'second-amazon-payment'      => ['css' => '.payment-list li:nth-of-type(2) a'],
            'go-to-billing'              => ['css' => 'button.continue.primary'],
            'first-shipping-method'      => ['css' => 'input[name="shipping_method"]:nth-of-type(1)'],
            'billing-address'            => ['css' => '.amazon-billing-address'],
            'full-screen-loader'         => ['css' => '.loading-mask'],
            'shipping-loader'            => ['css' => '.checkout-shipping-method._block-content-loading'],
            'revert-checkout'            => ['css' => '.revert-checkout'],
            'shipping-form'              => ['css' => '#co-shipping-form'],
            'pay-with-amazon'            => ['css' => '#OffAmazonPaymentsWidgets0'],
            'submit-order'               => ['css' => 'button.checkout.primary'],
            'customer-email-input'       => ['css' => 'input#customer-email'],
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
        $this->waitForAjaxRequestsToComplete();
        $this->waitUntilElementDisappear('full-screen-loader');
    }

    public function selectAlternativeAmazonPaymentMethod()
    {
        $this->waitForElement('alternative-payment-widget');
        $this->getDriver()->switchToIFrame('OffAmazonPaymentsWidgets2IFrame');
        $this->clickElement('second-amazon-payment');
        $this->getDriver()->switchToIFrame(null);
        $this->waitForAjaxRequestsToComplete();
        $this->waitUntilElementDisappear('full-screen-loader');
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

    public function submitOrder()
    {
        $this->clickElement('submit-order');
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

    public function hasPayWithAmazonButton()
    {
        try {
            $element = $this->getElementWithWait('pay-with-amazon', 30000);
            return $element->isVisible();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasPaymentWidget()
    {
        try {
            $element = $this->getElement('payment-widget');
            return $element->isVisible();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isLoggedIn()
    {
        try {
            return $this->getDriver()->evaluateScript(
                'require(\'uiRegistry\').get(\'checkout.steps.shipping-step.shippingAddress.before-form.amazon-widget-address\').isAmazonAccountLoggedIn();'
            );
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAmazonOrderRef()
    {
        $orderRef = $this->getDriver()->evaluateScript(
            'require(\'uiRegistry\').get(\'checkout.steps.shipping-step.shippingAddress.before-form.amazon-widget-address\').getAmazonOrderReference();'
        );

        if ( ! strlen($orderRef)) {
            throw new \Exception('Could not locate amazon order reference');
        }

        return $orderRef;
    }

    public function selectSimulation($simulation)
    {
        $this->waitUntilElementDisappear('full-screen-loader');
        $this->getElement('Checkout\SandboxSimulation')->selectSimulation($simulation);
    }

    /**
     * @return ShippingAddressForm
     */
    public function getShippingForm()
    {
        return $this->getElement('Checkout\ShippingAddressForm');
    }

    /**
     * @return PaymentMethodForm
     */
    public function getPaymentMethodForm()
    {
        return $this->getElement('Checkout\PaymentMethodForm');
    }

    /**
     * @param string $email
     * @throws \Exception
     */
    public function setCustomerEmail($email)
    {
        $input = $this->getElementWithWait('customer-email-input');

        if (!$input) {
            throw new \Exception('No customer email input was found.');
        }

        $input->setValue((string) $email);
    }
}
