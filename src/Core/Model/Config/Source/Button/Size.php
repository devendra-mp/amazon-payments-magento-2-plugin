<?php

namespace Amazon\Core\Model\Config\Source\Button;

use Magento\Framework\Option\ArrayInterface;

class Size implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'small', 'label' => __('Small')],
            ['value' => 'medium', 'label' => __('Medium')],
            ['value' => 'large', 'label' => __('Large')],
            ['value' => 'x-large', 'label' => __('Extra large')],
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
            'small' => __('Small'),
            'medium' => __('Medium'),
            'large' => __('Large'),
            'x-large' => __('Extra large'),
        ];
    }
}
