<?php

namespace Page;

use Behat\Mink\Driver\DriverInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

trait AmazonLoginTrait
{
    /**
     * Returns element's driver.
     *
     * @return DriverInterface
     */
    abstract protected function getDriver();

    /**
     * @param string $name
     *
     * @return Element
     */
    abstract public function clickElement($elementName);
    
    abstract public function waitForPageLoad($maxWait = 60000);

    abstract public function fillField($locator, $value);

    public function loginAmazonCustomer($email, $password)
    {
        $currentWindow = $this->getDriver()->getWindowName();
        
        $this->clickElement('open-amazon-login');
        
        $this->getDriver()->switchToWindow('amazonloginpopup');

        $this->fillField('ap_email', $email);
        $this->fillField('ap_password', $password);
        $this->clickElement('amazon-login');

        $this->getDriver()->switchToWindow($currentWindow);

        $this->waitForPageLoad();
    }

    public function hasLoginWithAmazonButton()
    {
        try {
            $element = $this->getElementWithWait('open-amazon-login', 5000);
            return $element->isVisible();
        } catch (\Exception $e) {
            return false;
        }
    }
}