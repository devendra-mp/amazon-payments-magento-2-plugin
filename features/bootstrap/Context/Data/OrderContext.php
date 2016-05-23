<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
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

    public function __construct()
    {
        $this->customerFixture    = new CustomerFixture;
        $this->orderFixture       = new OrderFixture;
        $this->transactionFixture = new TransactionFixture;
        $this->invoiceFixture     = new InvoiceFixture;
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
        $transaction = $this->getLastTransactionForLastOrder($email);

        PHPUnit_Framework_Assert::assertSame($transaction->getTxnType(), Transaction::TYPE_AUTH);
        PHPUnit_Framework_Assert::assertSame($transaction->getIsClosed(), '0');
    }

    /**
     * @Given there should be a closed authorization for the last order for :email
     */
    public function thereShouldBeAClosedAuthorizationForTheLastOrderFor($email)
    {
        $lastOrder = $this->getLastOrderForCustomer($email);
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
        $transaction = $this->getLastTransactionForLastOrder($email);

        PHPUnit_Framework_Assert::assertSame($transaction->getTxnType(), Transaction::TYPE_CAPTURE);
        PHPUnit_Framework_Assert::assertSame($transaction->getIsClosed(), '1');
    }

    /**
     * @Then there should be a paid invoice for the last order for :email
     */
    public function thereShouldBeAPaidInvoiceForTheLastOrderFor($email)
    {
        $transaction = $this->getLastTransactionForLastOrder($email);
        $invoice     = $this->invoiceFixture->getByTransactionId($transaction->getTxnId());

        PHPUnit_Framework_Assert::assertSame($invoice->getState(), (string)Invoice::STATE_PAID);
    }

    protected function getLastTransactionForLastOrder($email)
    {
        $lastOrder = $this->getLastOrderForCustomer($email);
        
        $transactionId = $lastOrder->getPayment()->getLastTransId();
        $paymentId     = $lastOrder->getPayment()->getId();
        $orderId       = $lastOrder->getId();

        $transaction = $this->transactionFixture->getByTransactionId($transactionId, $paymentId, $orderId);

        if ( ! $transaction) {
            throw new \Exception('Last transaction not found for ' . $email);
        }

        return $transaction;
    }

    protected function getLastOrderForCustomer($email)
    {
        $customer = $this->customerFixture->get($email);
        $orders   = $this->orderFixture->getForCustomer($customer);

        $order = current($orders->getItems());

        if ( ! $order) {
            throw new \Exception('Last order not found for ' . $email);
        }

        return $order;
    }
}