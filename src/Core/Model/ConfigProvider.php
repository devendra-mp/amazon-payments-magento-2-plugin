<?php

namespace Amazon\Core\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Amazon\Core\Helper\Data;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $coreHelper;

    public function __construct(
        Data $coreHelper
    ) {
        $this->coreHelper = $coreHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'amazonPayments' => [
                'merchantId' => $this->coreHelper->getMerchantId(),
                'clientId' => $this->coreHelper->getClientId(),
                'isPwaEnabled' => $this->coreHelper->isPwaEnabled(),
                'isLwaEnabled' => $this->coreHelper->isLwaEnabled(),
                'paymentAction' => $this->coreHelper->getPaymentAction(),
                'authorizationMode' => $this->coreHelper->getAuthorizationMode(),
                'displayLanguage' => $this->coreHelper->getDisplayLanguage(),
                'authenticationExperience' => $this->coreHelper->getAuthenticationExperience(),
                'buttonTypePwa' => $this->coreHelper->getButtonTypePwa(),
                'buttonTypeLwa' => $this->coreHelper->getButtonTypeLwa(),
                'buttonColor' => $this->coreHelper->getButtonColor(),
                'buttonSize' => $this->coreHelper->getButtonSize(),
                'redirectURL' => $this->coreHelper->getRedirectUrl(),
            ]
        ];

        return $config;
    }
}
