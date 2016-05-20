<?php

namespace Page\Element\Checkout;

use Behat\Mink\Element\NodeElement;
use Page\Element\ElementHelper;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class PaymentMethodForm extends Element
{
    use ElementHelper;

    protected $selector = 'form#co-payment-form';

    /**
     * @param string $paymentMethodCode e.g. "checkmo"
     * @param bool $strict
     * @throws \Exception
     */
    public function selectPaymentMethodByCode($paymentMethodCode, $strict = true)
    {
        /** @var NodeElement[] $paymentMethodRadios */
        $paymentMethodRadios = $this->findAll('css', 'input[name="payment[method]"]');

        foreach ($paymentMethodRadios as $paymentMethodRadio) {
            if ($paymentMethodRadio->getAttribute('value') === $paymentMethodCode) {
                $paymentMethodRadio->click();
                return;
            }
        }

        if ($strict) {
            throw new \Exception("Payment method with code $paymentMethodCode was not found");
        }
    }
}
