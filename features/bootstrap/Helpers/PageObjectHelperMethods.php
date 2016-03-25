<?php

namespace Helpers;

trait PageObjectHelperMethods
{
    function openPage($params = [])
    {
        $this->open($params);
        $this->waitForPageLoad();
    }

    function acceptAlert()
    {
        $this->getDriver()->getWebDriverSession()->accept_alert();
    }

    function waitForCondition($condition, $maxWait = 120000)
    {
        $this->getSession()->wait($maxWait, $condition);
    }

    function waitForPageLoad($maxWait = 120000)
    {
        $this->waitForCondition('(document.readyState == "complete") && (typeof window.jQuery == "function") && (jQuery.active == 0)', $maxWait);
    }

    function waitForElement($elementName, $maxWait = 120000)
    {
        $visibilityCheck = $this->getElementVisibilyCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && $visibilityCheck", $maxWait);
    }

    function waitUntilElementDisappear($elementName, $maxWait = 120000)
    {
        $visibilityCheck = $this->getElementVisibilyCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && !$visibilityCheck", $maxWait);
    }

    function waitTime($waitTime)
    {
        $this->getSession()->wait($waitTime);
    }

    function scrollToBottom()
    {
        $this->getSession()->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    }

    function clickElement($elementName)
    {
        $this->getElementWithWait($elementName)->click();
    }

    function getElementValue($elementName)
    {
        return $this->getElementWithWait($elementName)->getValue();
    }

    function setElementValue($elementName, $value)
    {
        $this->getElementWithWait($elementName)->setValue($value);
    }

    function getElementText($elementName)
    {
        return $this->getElementWithWait($elementName)->getText();
    }

    public function getElementWithWait($elementName, $waitTime = 2500)
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
