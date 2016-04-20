<?php

namespace Page;

use Behat\Mink\Driver\DriverInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

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

    public function waitForCondition($condition, $maxWait = 120000)
    {
        $this->getDriver()->wait($maxWait, $condition);
    }

    public function waitForPageLoad($maxWait = 120000)
    {
        $this->waitForCondition('(document.readyState == "complete") && (typeof window.jQuery == "function") && (jQuery.active == 0)', $maxWait);
    }

    public function waitForElement($elementName, $maxWait = 120000)
    {
        $visibilityCheck = $this->getElementVisibilyCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && $visibilityCheck", $maxWait);
    }

    public function waitUntilElementDisappear($elementName, $maxWait = 120000)
    {
        $visibilityCheck = $this->getElementVisibilyCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && !$visibilityCheck", $maxWait);
    }

    public function clickElement($elementName)
    {
        $this->getElementWithWait($elementName)->click();
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

    public function getElementWithWait($elementName, $waitTime = 120000)
    {
        $this->waitForElement($elementName, $waitTime);
        return $this->getElement($elementName);
    }

    public function getElementVisibilyCheck($elementName)
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
}