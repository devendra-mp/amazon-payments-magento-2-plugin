<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\Element\CurrencySwitcher;
use Page\Store\Home;
use Page\Store\Product;

class BasketContext implements SnippetAcceptingContext
{
    /**
     * @var Product
     */
    protected $productPage;

    /**
     * @var CurrencySwitcher
     */
    protected $currencySwitcherElement;

    /**
     * @var Home
     */
    protected $homePage;

    public function __construct(Product $productPage, CurrencySwitcher $currencySwitcherElement, Home $homePage)
    {
        $this->productPage             = $productPage;
        $this->currencySwitcherElement = $currencySwitcherElement;
        $this->homePage                = $homePage;
    }

    /**
     * @Given there is a valid product in my basket
     */
    public function thereIsAValidProductInMyBasket()
    {
        $this->productPage->open([
            'id' => 1
        ]);
        $this->productPage->addToBasket();
    }

    /**
     * @Given I want to pay using an unsupported currency
     */
    public function iWantToPayUsingAnUnsupportedCurrency()
    {
        $this->homePage->open();
        $this->currencySwitcherElement->selectCurrency('CHF');
    }
}