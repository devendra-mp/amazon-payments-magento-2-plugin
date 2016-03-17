<?php

namespace Amazon\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Amazon\Payment\Helper\Data;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $paymentHelper;

    public function __construct(
        Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'amazonPayment' => [
                    'isEnabled' => $this->paymentHelper->isEnabled(),
                    'buttonType' => $this->paymentHelper->getButtonType(),
                    'buttonColor' => $this->paymentHelper->getButtonColor(),
                    'buttonSize' => $this->paymentHelper->getButtonSize(),
                ]
            ]
        ];

        return $config;
    }
}
