<?php

namespace Amazon\Login\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Amazon\Core\Helper\Data;

class Login extends Template
{
    /**
     * @var Data
     */
    private $coreHelper;

    public function __construct(Context $context, Data $coreHelper) {
        $this->coreHelper = $coreHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isLwaEnabled()
    {
        return $this->coreHelper->isLwaEnabled();
    }

    /**
     * @return string
     */
    public function getButtonData()
    {
        $buttonData = [
            'buttonType' => $this->coreHelper->getButtonTypeLwa(),
            'buttonColor' => $this->coreHelper->getButtonColor(),
            'buttonSize' => $this->coreHelper->getButtonSize(),
            'redirectURL' => $this->coreHelper->getRedirectUrl()
        ];

        return json_encode($buttonData);
    }
}
