<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Fixtures\AmazonOrder as AmazonOrderFixture;
use Page\Store\Checkout;
use PHPUnit_Framework_Assert;

class AmazonContext implements SnippetAcceptingContext
{
    /**
     * @var Checkout
     */
    protected $checkoutPage;

    /**
     * @var AmazonOrderFixture
     */
    protected $amazonOrderFixture;

    public function __construct(Checkout $checkoutPage)
    {
        $this->checkoutPage       = $checkoutPage;
        $this->amazonOrderFixture = new AmazonOrderFixture;
    }

    /**
     * @Then my amazon order should be cancelled
     */
    public function myAmazonOrderShouldBeCancelled()
    {
        $orderRef   = $this->checkoutPage->getAmazonOrderRef();
        $orderState = $this->amazonOrderFixture->getState($orderRef);

        PHPUnit_Framework_Assert::assertSame($orderState, 'Canceled');
    }
}