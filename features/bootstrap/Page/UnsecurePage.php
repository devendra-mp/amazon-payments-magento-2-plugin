<?php

namespace Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page as BasePage;

class UnsecurePage extends BasePage
{
    protected function getUrl(array $urlParameters = array())
    {
        return str_replace('https:', 'http:', parent::getUrl($urlParameters));
    }
}