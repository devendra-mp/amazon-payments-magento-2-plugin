<?php

namespace Amazon\Login\Plugin;

use Closure;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\DB\Select;

class CustomerCollection
{
    public function aroundAddAttributeToFilter(
        Collection $collection, Closure $proceed, $attribute, $condition = null, $joinType = 'inner'
    ) {
        if (is_array($attribute)) {
            foreach ($attribute as $key => $condition) {
                if ('amazon_id' == $condition['attribute']) {
                    $collection->getSelect()->where('extension_attribute_amazon_id.amazon_id = ?', $condition['eq']);
                    unset($attribute[$key]);
                }
            }

            if (0 === count($attribute)) {
                return $collection;
            }
        }

        return $proceed($attribute, $condition, $joinType);
    }
}