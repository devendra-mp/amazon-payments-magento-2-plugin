<?php

namespace Context\Web\Store;

use Behat\Behat\Context\SnippetAcceptingContext;
use Bex\Behat\Magento2InitExtension\Fixtures\MagentoConfigManager;
use Page\Store\Checkout;
use Page\Store\Success;

class CustomerContext implements SnippetAcceptingContext
{
    /**
     * @var MagentoConfigManager
     */
    protected $m2Config;

    /**
     * @var Checkout
     */
    protected $checkoutPage;

    /**
     * @var Success
     */
    protected $successPage;

    /**
     * CustomerContext constructor.
     *
     * @param Checkout $checkoutPage
     * @param Success $successPage
     */
    public function __construct(Checkout $checkoutPage, Success $successPage)
    {
        $this->m2Config = new MagentoConfigManager;
        $this->checkoutPage = $checkoutPage;
        $this->successPage = $successPage;
    }

    /**
     * @Given Login with Amazon is disabled
     */
    public function loginWithAmazonIsDisabled()
    {
        $this->m2Config->changeConfigs([
            [
                'path' => 'payment/amazon_payment/lwa_enabled',
                'value' => '0',
                'scope_type' => 'default',
                'scope_code' => null,
            ]
        ]);
    }

    /**
     * @Then I can create a new Amazon account on the success page with email :email
     */
    public function iCanCreateANewAmazonAccountOnTheSuccessPageWithEmail($email)
    {
        $this->successPage->clickCreateAccountHavingEmail($email);
    }

    /**
     * @AfterScenario @revert-m2-config
     */
    public function revertM2Config()
    {
        $this->m2Config->revertAllConfig();
    }
}
