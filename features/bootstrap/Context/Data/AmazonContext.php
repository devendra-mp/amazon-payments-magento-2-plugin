<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\AmazonOrder as AmazonOrderFixture;
use Fixtures\Customer as CustomerFixture;
use Fixtures\Order as OrderFixture;
use Fixtures\Transaction as TransactionFixture;
use PHPUnit_Framework_Assert;

class AmazonContext implements SnippetAcceptingContext
{
    /**
     * @var AmazonOrderFixture
     */
    protected $amazonOrderFixture;

    /**
     * @var OrderFixture
     */
    protected $orderFixture;

    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    /**
     * @var TransactionFixture
     */
    protected $transactionFixture;

    public function __construct()
    {
        $this->customerFixture    = new CustomerFixture;
        $this->orderFixture       = new OrderFixture;
        $this->amazonOrderFixture = new AmazonOrderFixture;
        $this->transactionFixture = new TransactionFixture;
    }

    /**
     * @Then amazon should have an open authorization for the last order for :email
     */
    public function amazonShouldHaveAnOpenAuthorizationForTheLastOrderFor($email)
    {
        $authorizationId    = $this->getLastTransactionIdForLastOrder($email);
        $authorizationState = $this->amazonOrderFixture->getAuthrorizationState($authorizationId);

        PHPUnit_Framework_Assert::assertSame($authorizationState, 'Open');
    }

    /**
     * @Then amazon should have a complete capture for the last order for :email
     */
    public function amazonShouldHaveACompleteCaptureForTheLastOrderFor($email)
    {
        $captureId    = $this->getLastTransactionIdForLastOrder($email);
        $captureState = $this->amazonOrderFixture->getCaptureState($captureId);

        PHPUnit_Framework_Assert::assertSame($captureState, 'Completed');
    }

    /**
     * @Then amazon should have a refund for the last invoice for :email
     */
    public function amazonShouldHaveARefundForheLastInvoiceFor($email)
    {
        $transaction = $this->transactionFixture->getLastTransactionForLastOrder($email);
        $refundId    = $transaction->getTxnId();
        $refundState = $this->amazonOrderFixture->getRefundState($refundId);

        PHPUnit_Framework_Assert::assertSame($refundState, 'Pending');
    }

    protected function getLastTransactionIdForLastOrder($email)
    {
        $customer = $this->customerFixture->get($email);
        $orders   = $this->orderFixture->getForCustomer($customer);

        $lastOrder = current($orders->getItems());

        if ( ! $lastOrder) {
            throw new \Exception('Last order not found for ' . $email);
        }

        return $lastOrder->getPayment()->getLastTransId();
    }
}