<?php
namespace Context\General;

use Behat\MinkExtension\Context\RawMinkContext;
use Helpers\StoreBaseUrlProvider;

class BaseUrlInitContext extends RawMinkContext
{
    /**
     * @BeforeScenario
     */
    public function resetBaseUrl()
    {
        StoreBaseUrlProvider::resetBaseUrl();
    }

    /**
     * Given I am on the EU store
     */
    public function iAmOnTheEuStore()
    {
        StoreBaseUrlProvider::setBaseUrl(StoreBaseUrlProvider::EU_BASE_URL);
    }

    /**
     * @Given I am on the UK store
     */
    public function iAmOnTheUkStore()
    {
        StoreBaseUrlProvider::setBaseUrl(StoreBaseUrlProvider::UK_BASE_URL);
    }

    /**
     * @Given I am on the US store
     */
    public function iAmOnTheUsStore()
    {
        StoreBaseUrlProvider::setBaseUrl(StoreBaseUrlProvider::US_BASE_URL);
    }
}
