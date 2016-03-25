<?php

namespace Context\Store;

use Behat\Behat\Tester\Exception\PendingException;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;

use Page\Store\ProductPage;
use Page\Store\CheckoutPage;
use Fixtures\Order as OrderFixture;

class OrderContext extends PageObjectContext implements Context, SnippetAcceptingContext
{
    /**
     * @var ProductPage
     */
    private $productPage;

    /**
     * @var CheckoutPage
     */
    private $checkoutPage;

    /**
     * @var OrderFixture
     */
    private $orderFixture;

    /**
     * @param ProductPage  $productPage
     * @param CheckoutPage $checkoutPage
     */
    public function __construct(ProductPage $productPage, CheckoutPage $checkoutPage)
    {
        $this->productPage = $productPage;
        $this->checkoutPage = $checkoutPage;
        $this->orderFixture = new OrderFixture();
    }

    /**
     * @Given I place an order from the :country
     * @Given There is a new order from the :country
     */
    public function iPlaceAnOrder($country)
    {
        $this->productPage->openPage(['id' => 4]);
        $this->productPage->addToBasket();
        $this->checkoutPage->openPage();
        $this->checkoutPage->fillShippingFormWithTestData($country);
        $this->checkoutPage->placeOrder();
        if (!$this->checkoutPage->canSeeConfirmationPage()) {
            throw new \Exception("I can't see the order confirmation page");
        }
    }

    /**
     * @Then the order status should be :orderStatus
     * @Then the order status should change to :orderStatus
     */
    public function theOrderStatusShouldBe($orderStatus)
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        $order = $this->orderFixture->getOrderByIncrementId($orderNumber);
        if ($order->getStatus() != $orderStatus) {
            throw new \Exception("Unexpected order status: " . $order->getStatus());
        }
    }

    /**
     * @Then the order state should be :orderState
     */
    public function theOrderStateShouldBe($orderState)
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        $order = $this->orderFixture->getOrderByIncrementId($orderNumber);
        if ($order->getState() != $orderState) {
            throw new \Exception("Unexpected order state: " . $order->getState());
        }
    }

    /**
     * @When the order invoiced
     */
    public function theOrderInvoiced()
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        $this->orderFixture->invoiceOrder($orderNumber);
    }

    /**
     * @Given there is an order with status :orderStatus
     */
    public function thereIsAnOrderWithStatus($orderStatus)
    {
        $this->iPlaceAnOrder();
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        $this->orderFixture->invoiceOrder($orderNumber);
        $this->orderFixture->setOrderStatus($orderNumber, $orderStatus);
    }

    /**
     * @Then the order should be invoiced
     */
    public function theOrderShouldBeInvoiced()
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        $order = $this->orderFixture->getOrderByIncrementId($orderNumber);
        if (!$order->hasInvoices()) {
            throw new \Exception("Order has not been invoiced.");
        }
    }

    /**
     * @Then the order should be shipped
     */
    public function theOrderShouldBeShipped()
    {
        $orderNumber = $this->checkoutPage->getLastOrderNumber();
        $order = $this->orderFixture->getOrderByIncrementId($orderNumber);
        if (!$order->hasShipments()) {
            throw new \Exception("Order has not been shipped.");
        }
    }
}
