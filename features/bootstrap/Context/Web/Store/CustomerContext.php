<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Bex\Behat\Magento2InitExtension\Fixtures\MagentoConfigManager;
use Fixtures\Customer as CustomerFixture;
use Page\Store\Checkout;
use Page\Store\Success;

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
    }

    /**
     * @Then I can create a new Amazon account on the success page with email :email
     */
    public function iCanCreateANewAmazonAccountOnTheSuccessPageWithEmail($email)
    {
        $this->successPage->clickCreateAccount();
        $this->customerFixture->track($email);
    }
}
