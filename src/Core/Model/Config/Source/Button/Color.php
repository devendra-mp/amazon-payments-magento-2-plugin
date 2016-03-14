<?php

namespace Amazon\Core\Model\Config\Source\Button;

use Magento\Framework\Option\ArrayInterface;

class Color implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Gold', 'label' => __('Gold')],
            ['value' => 'LightGray', 'label' => __('Light gray')],
            ['value' => 'DarkGray', 'label' => __('Dark gray')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'Gold' => __('Gold'),
            'LightGray' => __('Light gray'),
            'DarkGray' => __('Dark gray'),
        ];
    }
}
