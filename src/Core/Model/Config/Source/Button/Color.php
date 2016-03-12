<?php

namespace Amazon\Core\Model\Config\Source\Button;

class Color implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
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
