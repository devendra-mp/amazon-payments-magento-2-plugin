<?php

namespace Page\Type;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use Helpers\StoreBaseUrlProvider;
use Helpers\PageObjectHelperMethods;

class StorePage extends Page
{
    use PageObjectHelperMethods;

    /**
     * @param Session $session
     * @param Factory $factory
     * @param array   $parameters
     */
    public function __construct(Session $session, Factory $factory, array $parameters = array())
    {
        $parameters['base_url'] = StoreBaseUrlProvider::getBaseUrl();
        parent::__construct($session, $factory, $parameters);
    }
}
