<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\Customer as CustomerFixture;
use Fixtures\Order as OrderFixture;
use Fixtures\Transaction as TransactionFixture;
use Magento\Customer\Api\Data\CustomerInterface;
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

    public function __construct()
    {
        $this->customerFixture    = new CustomerFixture;
        $this->orderFixture       = new OrderFixture;
        $this->transactionFixture = new TransactionFixture;
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
     * @Then there should be a closed capture for the last order for :email
     */
    public function thereShouldBeAClosedCaptureForTheLastOrderFor($email)
    {
        $transaction = $this->getLastTransactionForLastOrder($email);

        PHPUnit_Framework_Assert::assertSame($transaction->getTxnType(), Transaction::TYPE_CAPTURE);
        PHPUnit_Framework_Assert::assertSame($transaction->getIsClosed(), '1');
    }

    protected function getLastTransactionForLastOrder($email)
    {
        $customer = $this->customerFixture->get($email);
        $orders   = $this->orderFixture->getForCustomer($customer);

        $lastOrder     = current($orders->getItems());
        $transactionId = $lastOrder->getPayment()->getLastTransId();
        $paymentId     = $lastOrder->getPayment()->getId();
        $orderId       = $lastOrder->getId();

        return $this->transactionFixture->getByTransactionId($transactionId, $paymentId, $orderId);
    }
}