<?php

namespace Amazon\Core\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Url;
use Amazon\Core\Helper\Data;

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
            'widgetUrl' => $this->coreHelper->getWidgetUrl(),
            'merchantId' => $this->coreHelper->getMerchantId(),
            'clientId' => $this->coreHelper->getClientId(),
            'isPwaEnabled' => $this->coreHelper->isPwaEnabled(),
            'isLwaEnabled' => $this->coreHelper->isLwaEnabled(),
            'chargeOnOrder' => $this->sanitizePaymentAction(),
            'authorizationMode' => $this->coreHelper->getAuthorizationMode(),
            'displayLanguage' => $this->coreHelper->getDisplayLanguage(),
            'authenticationExperience' => $this->coreHelper->getAuthenticationExperience(),
            'buttonTypePwa' => $this->coreHelper->getButtonTypePwa(),
            'buttonTypeLwa' => $this->coreHelper->getButtonTypeLwa(),
            'buttonColor' => $this->coreHelper->getButtonColor(),
            'buttonSize' => $this->coreHelper->getButtonSize(),
            'redirectUrl' => $this->coreHelper->getRedirectUrl(),
            'loginPostUrl' => $this->url->getLoginPostUrl(),
        ];

        return $config;
    }

    /**
     * @return bool
     */
    public function isBadgeEnabled()
    {
        return ($this->coreHelper->isPwaEnabled() || $this->coreHelper->isLwaEnabled());
    }

    /**
     * @return bool
     */
    public function isPwaEnabled()
    {
        return $this->coreHelper->isPwaEnabled();
    }

    /**
     * @return bool
     */
    public function sanitizePaymentAction()
    {
        return ($this->coreHelper->getPaymentAction() === 'authorize_capture');
    }

    /**
     *
     * @return string
     */
    public function getWidgetUrl()
    {
        return $this->coreHelper->getWidgetUrl();
    }
}
