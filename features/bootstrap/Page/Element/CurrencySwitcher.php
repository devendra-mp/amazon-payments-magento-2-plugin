<?php

namespace Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class CurrencySwitcher extends Element
{
    protected $selector = '#switcher-currency';

    public function selectCurrency($code)
    {
        $this->find('css', '#switcher-currency-trigger')->click();
        $this->find('css', 'li.currency-' . $code . ' a')->click();
    }
}