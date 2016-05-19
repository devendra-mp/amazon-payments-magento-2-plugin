<?php

namespace Page\Store\Element\Checkout;

use Behat\Mink\Element\NodeElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class ShippingAddressForm extends Element
{
    protected $selector = 'form#co-shipping-form';

    /**
     * @param string $firstName
     * @return $this
     */
    public function withFirstName($firstName)
    {
        $this->findElement('input[name="firstname"]')->setValue((string) $firstName);
        return $this;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function withLastName($lastName)
    {
        $this->findElement('input[name="lastname"]')->setValue((string) $lastName);
        return $this;
    }

    /**
     * @param string $company
     * @return $this
     */
    public function withCompany($company)
    {
        $this->findElement('input[name="company"]')->setValue((string) $company);
        return $this;
    }

    /**
     * @param array $addressLines
     * @param bool $strict
     * @return $this
     */
    public function withAddressLines(array $addressLines, $strict = false)
    {
        // reset to numeric keys
        $addressLines = array_values($addressLines);

        foreach ($addressLines as $lineNumber => $addressLine) {
            $addressLineElement = $this->findElement(sprintf('input[name="street[%d]"]', $lineNumber), $strict);

            if ($addressLineElement === null) {
                continue;
            }

            $addressLineElement->setValue((string) $addressLine);
        }

        return $this;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function withCity($city)
    {
        $this->findElement('input[name="city"]')->setValue((string) $city);
        return $this;
    }

    /**
     * @param string $state e.g. "Washington", "Texas"
     * @return $this
     * @throws \Exception
     */
    public function withState($state)
    {
        if ($regionTextElement = $this->findElement('input[name="region_id"]', false)) {
            $regionTextElement->setValue((string) $state);
        } elseif ($regionSelectElement = $this->findElement('select[name="region_id"]', false)) {
            $regionSelectElement->selectOption((string) $state);
        } else {
            throw new \Exception("Could not find State element.");
        }

        return $this;
    }

    /**
     * @param string $postCode
     * @return $this
     */
    public function withPostCode($postCode)
    {
        $this->findElement('input[name="postcode"]')->setValue((string) $postCode);
        return $this;
    }

    /**
     * @param string $country e.g. GB, US, DE
     * @return $this
     */
    public function withCountry($country)
    {
        $country = strtoupper($country);
        $this->findElement('select[name="country_id"]')->selectOption($country);
        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function withPhoneNumber($phoneNumber)
    {
        $this->findElement('input[name="telephone"]')->setValue((string) $phoneNumber);
        return $this;
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
