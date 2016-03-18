<?php

namespace Amazon\Login\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Amazon\Login\Helper\Data;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $loginHelper;

    public function __construct(
        Data $loginHelper
    ) {
        $this->loginHelper = $loginHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'login' => [
                'amazonLogin' => [
                    'isEnabled' => $this->loginHelper->isEnabled(),
                    'buttonType' => $this->loginHelper->getButtonType(),
                    'buttonColor' => $this->loginHelper->getButtonColor(),
                    'buttonSize' => $this->loginHelper->getButtonSize(),
                    'redirectURL' => $this->loginHelper->getRedirectUrl(),
                ]
            ]
        ];

        return $config;
    }
}
