<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Currency as CurrencyFixture;
use Page\Element\CurrencySwitcher;
use Page\Store\Basket;
use Page\Store\Home;
use Page\Store\Product;
use PHPUnit_Framework_Assert;

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

    /**
     * @var Basket
     */
    protected $basketPage;

    /**
     * @param Product          $productPage
     * @param CurrencySwitcher $currencySwitcherElement
     * @param Home             $homePage
     * @param Basket           $basketPage
     */
    public function __construct(
        Product $productPage,
        CurrencySwitcher $currencySwitcherElement,
        Home $homePage,
        Basket $basketPage
    ) {
        $this->productPage             = $productPage;
        $this->currencySwitcherElement = $currencySwitcherElement;
        $this->homePage                = $homePage;
        $this->currencyFixture         = new CurrencyFixture;
        $this->basketPage              = $basketPage;
    }

    /**
     * @Given there is a valid product in my basket
     */
    public function thereIsAValidProductInMyBasket()
    {
        $this->productPage->openWithProductId(1);
        $this->productPage->addToBasket();
    }

    /**
     * @Given I go to my basket
     */
    public function iGoToMyBasket()
    {
        $this->basketPage->open();
    }

    /**
     * @Then I see a login with amazon button on the basket page
     */
    public function iSeeALoginWithAmazonButtonOnTheBasketPage()
    {
        $hasLwa = $this->basketPage->hasLoginWithAmazonButton();
        PHPUnit_Framework_Assert::assertTrue($hasLwa);
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

    /**
     * @Then I should be redirected to the Basket
     */
    public function iShouldBeRedirectedToTheBasket()
    {
        $this->basketPage->isOpen();
    }
}
