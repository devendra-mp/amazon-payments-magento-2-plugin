<?php

namespace Amazon\Core\Model\Config\Source\Button\Type;

use Magento\Framework\Option\ArrayInterface;

class Login implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'LwA', 'label' => __('Login with Amazon')],
            ['value' => 'Login', 'label' => __('Login')],
            ['value' => 'A', 'label' => __('Amazon logo')],
        ];
    }
}
