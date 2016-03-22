<?php

namespace Context\Store;

use Behat\Behat\Tester\Exception\PendingException;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Page\Store\ProductPage;
use Fixtures\Product as ProductFixture;

class ProductContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    /**
     * @var ProductPage
     */
    private $productPage;

    /**
     * @var ProductFixture
     */
    private $productFixture;

    /**
     * @param ProductPage $productPage
     */
    public function __construct(ProductPage $productPage)
    {
        $this->productPage = $productPage;
        $this->productFixture = new ProductFixture();
    }

    /**
     * @Given I am on the product page of the :sku product
     */
    public function iAmOnTheProductPageOfTheProduct($sku)
    {
        $id = $this->productFixture->getProductIdBySku($sku);
        $this->productPage->openPage(['id' => $id]);
    }

    /**
     * @Given I add the product to the basket
     */
    public function iAddTheProductToTheBasket()
    {
        $this->productPage->addToBasket();
    }

    /**
     * @Then the stock of the product with sku :sku should change to :qty for :warehouseCode warehouse
     */
    public function theProductStockShouldBe($sku, $qty, $warehouseCode)
    {
        $this->productFixture->checkQtyIs($sku, $qty, $warehouseCode);
    }

    /**
     * @Then the stock status of the product with sku :sku should change to :stockstatus for :warehouseCode warehouse
     */
    public function theProductShouldHaveStockStatus($sku, $stockstatus, $warehouseCode)
    {
        $this->productFixture->checkStockStatusIs($sku, $stockstatus, $warehouseCode);
    }
}
