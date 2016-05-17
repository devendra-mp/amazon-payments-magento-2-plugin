<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Directory\Model\CurrencyFactory;

class Currency extends BaseFixture
{
    public function saveRates(array $rates)
    {
        $factory = $this->getMagentoObject(CurrencyFactory::class);
        $factory->create()->saveRates($rates);
    }
}