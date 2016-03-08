<?php

namespace spec\Amazon\Login\Model;

use Amazon\Login\Api\Data\CustomerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CustomerSpec extends ObjectBehavior
{
    function let(Context $context, Registry $registry, AbstractResource $resource, AbstractDb $abstractDb)
    {
        $this->beConstructedWith($context, $registry, $resource, $abstractDb);
    }

    function it_is_a_magento_model()
    {
        $this->shouldHaveType(AbstractModel::class);
    }

    function it_is_an_amazon_customer()
    {
        $this->shouldHaveType(CustomerInterface::class);
    }
}
