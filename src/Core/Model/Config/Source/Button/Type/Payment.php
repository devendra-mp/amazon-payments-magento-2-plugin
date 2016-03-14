<?php

namespace Amazon\Core\Model\Config\Source\Button\Type;

use Magento\Framework\Option\ArrayInterface;

class Payment implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'PwA', 'label' => __('Pay with Amazon')],
            ['value' => 'Pay', 'label' => __('Pay')],
            ['value' => 'A', 'label' => __('Amazon logo')],
        ];
    }
}
