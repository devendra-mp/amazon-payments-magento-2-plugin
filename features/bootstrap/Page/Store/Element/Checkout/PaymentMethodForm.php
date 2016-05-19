<?php

namespace Page\Store\Element\Checkout;

use Behat\Mink\Element\NodeElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class PaymentMethodForm extends Element
{
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

    /**
     * @param string $cssQuery
     * @param bool $strict
     * @return NodeElement
     * @throws \Exception
     */
    protected function findElement($cssQuery, $strict = true)
    {
        $element = $this->find('css', $cssQuery);

        if ($strict && $element === null) {
            throw new \Exception('No element found with CSS query: ' . $cssQuery);
        }

        return $element;
    }
}
