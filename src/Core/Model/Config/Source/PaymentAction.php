<?php

namespace Amazon\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentAction implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'authorize', 'label' => __('Charge on Shipment')],
            ['value' => 'authorize_capture', 'label' => __('Charge on Order')],
        ];
    }
}
