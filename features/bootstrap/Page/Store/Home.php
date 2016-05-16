<?php

namespace Page\Store;

use Page\PageTrait;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Home extends Page
{
    use PageTrait;

    protected $path = '/';
}
