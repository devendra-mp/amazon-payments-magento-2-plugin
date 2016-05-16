<?php

namespace Amazon\Payment\Plugin;

use Magento\Quote\Model\Quote\Payment;

class AdditionalInformation
{
    const KEY_SANDBOX_SIMULATION_REFERENCE = 'sandbox_simulation_reference';

    protected $additionalKeys = [
        self::KEY_SANDBOX_SIMULATION_REFERENCE
    ];

    public function afterGetAdditionalInformation(Payment $subject, $result)
    {
        if (is_array($result)) {
            foreach ($this->additionalKeys as $additionalKey) {
                if ( ! array_key_exists($additionalKey, $result) && $subject->hasData($additionalKey)) {
                    $result[$additionalKey] = $subject->getDataUsingMethod($additionalKey);
                }
            }
        }
        return $result;
    }
}