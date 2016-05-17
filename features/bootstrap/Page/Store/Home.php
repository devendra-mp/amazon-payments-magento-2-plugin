<?php

namespace Page\Store;

use Page\PageTrait;
use Page\UnsecurePage;

class Home extends UnsecurePage
{
    use PageTrait;

    protected $path = '/';
}
