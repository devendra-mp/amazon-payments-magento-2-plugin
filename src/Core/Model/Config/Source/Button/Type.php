<?php

namespace Amazon\Core\Model\Config\Source\Button;

use Magento\Framework\Option\ArrayInterface;

class Type implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'full', 'label' => __('Login with Amazon / Pay with Amazon')],
            ['value' => 'short', 'label' => __('Login / Pay')],
            ['value' => 'logo', 'label' => __('Amazon Logo')],
        ];
    }
}
