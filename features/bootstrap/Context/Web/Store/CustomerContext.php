<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\AmazonOrder as AmazonOrderFixture;
use Fixtures\Customer as CustomerFixture;
use Fixtures\QuoteLink as QuoteLinkFixture;
use Page\Store\Checkout;
use Page\Store\Success;
use Fixtures\Order as OrderFixture;
use PHPUnit_Framework_Assert;

class CustomerContext implements SnippetAcceptingContext
{
    /**
     * @var Checkout
     */
    protected $checkoutPage;

    /**
     * @var Success
     */
    protected $successPage;

    /**
     * @var CustomerFixture
     */
    protected $customerFixture;

    /**
     * @var OrderFixture
     */
    protected $orderFixture;

    /**
     * @var AmazonOrderFixture
     */
    protected $amazonOrderFixture;

    /**
     * @var QuoteLinkFixture
     */
    protected $quoteLinkFixture;

    /**
     * CustomerContext constructor.
     *
     * @param Checkout $checkoutPage
     * @param Success  $successPage
     */
    public function __construct(Checkout $checkoutPage, Success $successPage)
    {
        $this->checkoutPage = $checkoutPage;
        $this->successPage  = $successPage;
        $this->customerFixture = new CustomerFixture;
        $this->orderFixture = new OrderFixture;
        $this->amazonOrderFixture = new AmazonOrderFixture;
        $this->quoteLinkFixture = new QuoteLinkFixture;

    }

    /**
     * @Then I can create a new Amazon account on the success page with email :email
     */
    public function iCanCreateANewAmazonAccountOnTheSuccessPageWithEmail($email)
    {
        $this->successPage->clickCreateAccount();
        $this->customerFixture->track($email);
    }

    /**
     * @Given the order for :email should be confirmed
     */
    public function theOrderForShouldBeConfirmed($email)
    {
        $order = $this->orderFixture->getLastOrderForCustomer($email);

        $orderRef = $order->getExtensionAttributes()->getAmazonOrderReferenceId();

        PHPUnit_Framework_Assert::assertNotEmpty($orderRef, 'Empty Amazon Order reference');
        $quoteLink = $this->quoteLinkFixture->getByColumnValue('amazon_order_reference_id', $orderRef);

        PHPUnit_Framework_Assert::assertNotEmpty(
            $quoteLink->getId(),
            "Quote Link with Amazon order reference $orderRef was not found"
        );

        PHPUnit_Framework_Assert::assertTrue($quoteLink->isConfirmed());

        $orderState = $this->amazonOrderFixture->getState($orderRef);

        PHPUnit_Framework_Assert::assertSame($orderState, 'Open');
    }
}
