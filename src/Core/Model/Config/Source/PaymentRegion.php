<?php

namespace Amazon\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentRegion implements ArrayInterface
{
    const UNDEFINED_OPTION_LABEL = '-- Please Select --';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __(self::UNDEFINED_OPTION_LABEL)],
            ['value' => 'de', 'label' => __('Euro Region')],
            ['value' => 'uk', 'label' => __('United Kingdom')],
            ['value' => 'us', 'label' => __('United States')],
            ['value' => 'jp', 'label' => __('Japan')],
        ];
    }
}
