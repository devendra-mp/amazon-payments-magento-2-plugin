<?php

namespace Amazon\Core\Model\Config\Source\Button\Type;

class Login implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'LwA', 'label' => __('Login with Amazon')],
            ['value' => 'Login', 'label' => __('Login')],
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
            'LwA' => __('Login with Amazon'),
            'Login' => __('Login'),
            'A' => __('Amazon logo'),
        ];
    }
}
