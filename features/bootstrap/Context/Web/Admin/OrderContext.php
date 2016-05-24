<?php

namespace Context\Web\Admin;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Fixtures\Invoice as InvoiceFixture;
use Fixtures\Order as OrderFixture;
use Page\Admin\CreditMemo;
use Page\Admin\Invoice;
use Page\Admin\Order;
use PHPUnit_Framework_Assert;

class OrderContext implements SnippetAcceptingContext
{
    /**
     * @var Order
     */
    protected $orderPage;

    /**
     * @var Invoice
     */
    protected $invoicePage;

    /**
     * @var CreditMemo
     */
    protected $creditMemoPage;

    public function __construct(Order $orderPage, Invoice $invoicePage, CreditMemo $creditMemoPage)
    {
        $this->orderPage       = $orderPage;
        $this->invoicePage     = $invoicePage;
        $this->creditMemoPage  = $creditMemoPage;
        $this->customerFixture = new CustomerFixture;
        $this->orderFixture    = new OrderFixture;
        $this->invoiceFixture  = new InvoiceFixture;
    }

    /**
     * @Given I go to invoice the last order for :email
     */
    public function iGoToInvoiceTheLastOrderFor($email)
    {
        $lastOrder = $this->orderFixture->getLastOrderForCustomer($email);

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

    /**
     * @Given I go to refund the last invoice for :email
     */
    public function iGoToRefundTheLastInvoiceFor($email)
    {
        $lastOrder   = $this->orderFixture->getLastOrderForCustomer($email);
        $lastInvoice = $this->invoiceFixture->getLastForOrder($lastOrder->getId());

        $this->invoicePage->openWithInvoiceId($lastInvoice->getId());
        $this->invoicePage->openCreateCreditMemo();
    }

    /**
     * @Given I submit my refund
     */
    public function iSubmitMyRefund()
    {
        $this->creditMemoPage->submitCreditMemo();
    }
}