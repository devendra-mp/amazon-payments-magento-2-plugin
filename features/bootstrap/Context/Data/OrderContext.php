<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\CreditMemo as CreditMemoFixture;
use Fixtures\Customer as CustomerFixture;
use Fixtures\Invoice as InvoiceFixture;
use Fixtures\Order as OrderFixture;
use Fixtures\Transaction as TransactionFixture;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction;
use PHPUnit_Framework_Assert;

class OrderContext implements SnippetAcceptingContext
{
    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    /**
     * @var OrderFixture
     */
    protected $orderFixture;

    /**
     * @var TransactionFixture
     */
    protected $transactionFixture;

    /**
     * @var invoiceFixture
     */
    protected $invoiceFixture;

    /**
     * @var CreditMemoFixture
     */
    protected $creditMemoFixture;

    public function __construct()
    {
        $this->customerFixture    = new CustomerFixture;
        $this->orderFixture       = new OrderFixture;
        $this->transactionFixture = new TransactionFixture;
        $this->invoiceFixture     = new InvoiceFixture;
        $this->creditMemoFixture  = new CreditMemoFixture;
    }

    /**
     * @Given :email should not have placed an order
     */
    public function shouldNotHavePlacedAnOrder($email)
    {
        $customer = $this->customerFixture->get($email);
        $orders   = $this->orderFixture->getForCustomer($customer);

        $orderCount = count($orders->getItems());

        PHPUnit_Framework_Assert::assertSame($orderCount, 0);
    }

    /**
     * @Then :email should have placed an order
     */
    public function shouldHavePlacedAnOrder($email)
    {
        $customer = $this->customerFixture->get($email);
        $orders   = $this->orderFixture->getForCustomer($customer);

        $orderCount = count($orders->getItems());

        PHPUnit_Framework_Assert::assertSame($orderCount, 1);
    }

    /**
     * @Then there should be an open authorization for the last order for :email
     */
    public function thereShouldBeAnOpenAuthorizationForTheLastOrderFor($email)
    {
        $transaction = $this->transactionFixture->getLastTransactionForLastOrder($email);

        PHPUnit_Framework_Assert::assertSame($transaction->getTxnType(), Transaction::TYPE_AUTH);
        PHPUnit_Framework_Assert::assertSame($transaction->getIsClosed(), '0');
    }

    /**
     * @Given there should be a closed authorization for the last order for :email
     */
    public function thereShouldBeAClosedAuthorizationForTheLastOrderFor($email)
    {
        $lastOrder = $this->orderFixture->getLastOrderForCustomer($email);
        $paymentId = $lastOrder->getPayment()->getId();
        $orderId   = $lastOrder->getId();

        $transaction = $this->transactionFixture->getByTransactionType(Transaction::TYPE_AUTH, $paymentId, $orderId);
        PHPUnit_Framework_Assert::assertSame($transaction->getIsClosed(), '1');
    }

    /**
     * @Then there should be a closed capture for the last order for :email
     */
    public function thereShouldBeAClosedCaptureForTheLastOrderFor($email)
    {
        $transaction = $this->transactionFixture->getLastTransactionForLastOrder($email);

        PHPUnit_Framework_Assert::assertSame($transaction->getTxnType(), Transaction::TYPE_CAPTURE);
        PHPUnit_Framework_Assert::assertSame($transaction->getIsClosed(), '1');
    }

    /**
     * @Then there should be a paid invoice for the last order for :email
     */
    public function thereShouldBeAPaidInvoiceForTheLastOrderFor($email)
    {
        $transaction = $this->transactionFixture->getLastTransactionForLastOrder($email);
        $invoice     = $this->invoiceFixture->getByTransactionId($transaction->getTxnId());

        PHPUnit_Framework_Assert::assertSame($invoice->getState(), (string)Invoice::STATE_PAID);
    }

    /**
     * @Then there should be a credit memo for the value of the last invoice for :email
     */
    public function thereShouldBeACreditMemoForTheValueOfTheLastInvoiceFor($email)
    {
        $lastOrder      = $this->orderFixture->getLastOrderForCustomer($email);
        $lastInvoice    = $this->invoiceFixture->getLastForOrder($lastOrder->getId());
        $lastCreditMemo = $this->creditMemoFixture->getLastForOrder($lastOrder->getId());

        PHPUnit_Framework_Assert::assertSame($lastInvoice->getBaseGrandTotal(), $lastCreditMemo->getBaseGrandTotal());
    }
}