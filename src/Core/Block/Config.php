<?php

namespace Amazon\Core\Block;

use Amazon\Core\Helper\Data;
use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Config extends Template
{
    /**
     * @var Data
     */
    protected $coreHelper;

    /**
     * @var Url
     */
    protected $url;

    public function __construct(Context $context, Data $coreHelper, Url $url) {
        $this->coreHelper = $coreHelper;
        $this->url = $url;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        $config = [
            'widgetUrl'                => $this->coreHelper->getWidgetUrl(),
            'merchantId'               => $this->coreHelper->getMerchantId(),
            'clientId'                 => $this->coreHelper->getClientId(),
            'isPwaEnabled'             => $this->coreHelper->isPaymentButtonEnabled(),
            'isLwaEnabled'             => $this->coreHelper->isLoginButtonEnabled(),
            'isSandboxEnabled'         => $this->coreHelper->isSandboxEnabled(),
            'chargeOnOrder'            => $this->sanitizePaymentAction(),
            'authorizationMode'        => $this->coreHelper->getAuthorizationMode(),
            'displayLanguage'          => $this->coreHelper->getDisplayLanguage(),
            'authenticationExperience' => $this->coreHelper->getAuthenticationExperience(),
            'buttonTypePwa'            => $this->coreHelper->getButtonTypePwa(),
            'buttonTypeLwa'            => $this->coreHelper->getButtonTypeLwa(),
            'buttonColor'              => $this->coreHelper->getButtonColor(),
            'buttonSize'               => $this->coreHelper->getButtonSize(),
            'redirectUrl'              => $this->coreHelper->getRedirectUrl(),
            'loginPostUrl'             => $this->url->getLoginPostUrl(),
            'sandboxSimulationOptions' => [],
            'loginScope'               => $this->coreHelper->getLoginScope(),
            'isEuPaymentRegion'        => $this->coreHelper->isEuPaymentRegion()
        ];

        if ($this->coreHelper->isSandboxEnabled()) {
            $config['sandboxSimulationOptions'] = $this->transformSandboxSimulationOptions();
        }

        return $config;
    }

    /**
     * @return bool
     */
    public function isBadgeEnabled()
    {
        return ($this->coreHelper->isPwaEnabled());
    }
    
    /**
     * @return bool
     */
    public function sanitizePaymentAction()
    {
        return ($this->coreHelper->getPaymentAction() === 'authorize_capture');
    }

    /**
     * @return array
     */
    public function transformSandboxSimulationOptions()
    {
        $sandboxSimulationOptions = $this->coreHelper->getSandboxSimulationOptions();
        $output = [];

        foreach ($sandboxSimulationOptions as $key => $value) {
            $output[] = [
                'labelText'       => $value,
                'simulationValue' => $key,
            ];
        }

        return $output;
    }
}
