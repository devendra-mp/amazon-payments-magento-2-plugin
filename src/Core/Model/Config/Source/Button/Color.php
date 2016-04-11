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
            ['value' => 'LightGray', 'label' => __('Light Gray')],
            ['value' => 'DarkGray', 'label' => __('Dark Gray')],
        ];
    }
}
