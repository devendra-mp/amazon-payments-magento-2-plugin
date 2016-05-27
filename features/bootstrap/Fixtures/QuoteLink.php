<?php

namespace Fixtures;

use Amazon\Payment\Api\Data\QuoteLinkInterface;
use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;

class QuoteLink extends BaseFixture
{
    /**
     * @param string $column
     * @param string $value
     * @return QuoteLinkInterface
     */
    public function getByColumnValue($column, $value)
    {
        return $this->getMagentoObject(QuoteLinkInterface::class)
                    ->load($value, $column);
    }
}
