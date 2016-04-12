<?php

namespace Amazon\Core\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Amazon\Core\Helper\Data;

class Config extends Template
{
    /**
     * @var Data
     */
    protected $coreHelper;

    public function __construct(Context $context, Data $coreHelper) {
        $this->coreHelper = $coreHelper;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        $config = [
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
        ];

        return $config;
    }

    /**
     * @return bool
     */
    public function isBadgeEnabled()
    {
        return ($this->coreHelper->isPwaEnabled() && $this->coreHelper->isLwaEnabled());
    }
}
