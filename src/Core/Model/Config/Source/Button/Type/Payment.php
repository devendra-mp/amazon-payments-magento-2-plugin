<?php

namespace Amazon\Core\Model\Config\Source\Button\Type;

class Payment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'PwA', 'label' => __('Pay with Amazon')],
            ['value' => 'Pay', 'label' => __('Pay')],
            ['value' => 'A', 'label' => __('Amazon logo')],
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
            'PwA' => __('Pay with Amazon'),
            'Pay' => __('Pay'),
            'A' => __('Amazon logo'),
        ];
    }
}
