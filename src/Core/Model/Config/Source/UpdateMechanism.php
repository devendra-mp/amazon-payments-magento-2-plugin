<?php

namespace Amazon\Core\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class UpdateMechanism implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'instant', 'label' => __('Instant Payment Notifications')],
            ['value' => 'polling', 'label' => __('Data Polling via Cron Job')],
        ];
    }
}
