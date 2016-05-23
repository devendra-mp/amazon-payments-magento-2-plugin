<?php

namespace Context\Web\Admin;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Fixtures\Order as OrderFixture;
use Page\Admin\Order;
use PHPUnit_Framework_Assert;

class OrderContext implements SnippetAcceptingContext
{
    /**
     * @var Order
     */
    protected $orderPage;

    public function __construct(Order $orderPage)
    {
        $this->orderPage       = $orderPage;
        $this->customerFixture = new CustomerFixture;
        $this->orderFixture    = new OrderFixture;
    }

    /**
     * @Given I go to invoice the last order for :email
     */
    public function iGoToInvoiceTheLastOrderFor($email)
    {
        $customer  = $this->customerFixture->get($email);
        $orders    = $this->orderFixture->getForCustomer($customer);
        $lastOrder = current($orders->getItems());

        if ( ! $lastOrder) {
            throw new \Exception('Last order not found for ' . $email);
        }

        $orderId = $lastOrder->getId();

        $this->orderPage->openWithOrderId($orderId);
        $this->orderPage->openCreateInvoice();
    }

    /**
     * @Given I submit my invoice
     */
    public function iSubmitMyInvoice()
    {
        $this->orderPage->submitInvoice();
    }
}