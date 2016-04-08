<?php

namespace Page\Type;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;

use Fixtures\AdminUrl;
use Helpers\PageObjectHelperMethods;

class AdminPage extends Page
{
    use PageObjectHelperMethods;

    const ADMIN_BASE_URL = 'http://amazon-payment.dev';

    /**
     * @param Session $session
     * @param Factory $factory
     * @param array   $parameters
     */
    public function __construct(Session $session, Factory $factory, array $parameters = array())
    {
        $parameters['base_url'] = self::ADMIN_BASE_URL;
        parent::__construct($session, $factory, $parameters);
    }
}
