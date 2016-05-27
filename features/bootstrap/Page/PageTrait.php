<?php

namespace Page;

use Behat\Mink\Driver\DriverInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;

trait PageTrait
{
    /**
     * @return DriverInterface
     */
    abstract protected function getDriver();

    /**
     * @param string $name
     *
     * @return Element
     */
    abstract public function getElement($name);

    public function waitForCondition($condition, $maxWait = 60000)
    {
        $this->getDriver()->wait($maxWait, $condition);
    }

    public function waitForPageLoad($maxWait = 60000)
    {
        $this->waitForCondition('(document.readyState == "complete") && (typeof window.jQuery == "function") && (jQuery.active == 0)', $maxWait);
    }

    public function waitForElement($elementName, $maxWait = 60000)
    {
        $visibilityCheck = $this->getElementVisibilityCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && $visibilityCheck", $maxWait);
    }

    public function waitUntilElementDisappear($elementName, $maxWait = 60000)
    {
        $visibilityCheck = $this->getElementVisibilityCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && !$visibilityCheck", $maxWait);
    }

    public function clickElement($elementName)
    {
        $element = $this->getElementWithWait($elementName);

        if ( ! $element) {
            throw new ElementNotFoundException;
        }

        $element->click();
    }

    public function getElementValue($elementName)
    {
        return $this->getElementWithWait($elementName)->getValue();
    }

    public function setElementValue($elementName, $value)
    {
        $this->getElementWithWait($elementName)->setValue($value);
    }

    public function getElementText($elementName)
    {
        return $this->getElementWithWait($elementName)->getText();
    }

    public function getElementWithWait($elementName, $waitTime = 60000)
    {
        $this->waitForElement($elementName, $waitTime);
        return $this->getElement($elementName);
    }

    public function getElementVisibilityCheck($elementName)
    {
        $visibilityCheck = 'true';

        if (isset($this->elements[$elementName]['css'])) {
            $elementFinder = $this->elements[$elementName]['css'];
            $visibilityCheck = "jQuery('$elementFinder').is(':visible')";
        }

        if (isset($this->elements[$elementName]['xpath'])) {
            $elementFinder = $this->elements[$elementName]['xpath'];
            $visibilityCheck = "jQuery(document.evaluate('$elementFinder', document, null, XPathResult.ANY_TYPE, null).iterateNext()).is(':visible')";
        }

        return $visibilityCheck;
    }

    public function isElementVisible($elementName)
    {
        $xpath = $this->getElement($elementName)->getXpath();
        return $this->getDriver()->isVisible($xpath);
    }

    public function waitForAjaxRequestsToComplete($maxWait = 60000)
    {
        $this->getDriver()->wait($maxWait, 'jQuery.active == 0');
    }
}