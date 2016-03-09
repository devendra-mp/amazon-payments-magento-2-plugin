<?php

namespace spec\Amazon\Login\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InstallSchemaSpec extends ObjectBehavior
{
    function it_is_an_installer()
    {
        $this->shouldHaveType(InstallSchemaInterface::class);
    }
}
