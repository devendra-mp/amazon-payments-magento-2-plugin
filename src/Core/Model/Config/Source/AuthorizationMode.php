<?php

namespace Amazon\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class AuthorizationMode implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'synchronous', 'label' => __('Synchronous')],
            ['value' => 'asynchronous', 'label' => __('Asynchronous')],
            ['value' => 'synchronous_possible', 'label' => __('Synchronous if Possible')],
            ['value' => 'none', 'label' => __('None')],
        ];
    }
}
