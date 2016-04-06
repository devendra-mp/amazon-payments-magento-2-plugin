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
            ['value' => 'shipment', 'label' => __('Charge on Shipment')],
            ['value' => 'order', 'label' => __('Charge on Order')],
        ];
    }
}
