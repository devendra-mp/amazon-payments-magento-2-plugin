<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Currency as CurrencyFixture;
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

    /**
     * @var CurrencyFixture
     */
    protected $currencyFixture;

    public function __construct(Product $productPage, CurrencySwitcher $currencySwitcherElement, Home $homePage)
    {
        $this->productPage             = $productPage;
        $this->currencySwitcherElement = $currencySwitcherElement;
        $this->homePage                = $homePage;
        $this->currencyFixture         = new CurrencyFixture;
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
        $rates = [
            'GBP' => [
                'CHF' => '1.41'
            ]
        ];

        $this->currencyFixture->saveRates($rates);

        $this->homePage->open();
        $this->currencySwitcherElement->selectCurrency('CHF');
    }
}