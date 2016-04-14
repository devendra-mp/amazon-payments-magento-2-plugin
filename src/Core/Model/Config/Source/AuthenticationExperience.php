<?php

namespace Amazon\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class AuthenticationExperience implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'popup', 'label' => __('Popup')],
            ['value' => 'redirect', 'label' => __('Redirect')],
        ];
    }
}
