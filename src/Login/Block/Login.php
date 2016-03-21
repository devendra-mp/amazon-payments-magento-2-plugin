<?php

namespace Amazon\Login\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Amazon\Login\Helper\Data;

class Login extends Template
{
    /**
     * @var Data
     */
    private $loginHelper;

    public function __construct(Context $context, Data $loginHelper) {
        $this->loginHelper = $loginHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->loginHelper->isEnabled();
    }

    /**
     * @return string
     */
    public function getButtonData()
    {
        $buttonData = [
            'buttonType' => $this->loginHelper->getButtonType(),
            'buttonColor' => $this->loginHelper->getButtonColor(),
            'buttonSize' => $this->loginHelper->getButtonSize(),
            'redirectURL' => $this->loginHelper->getRedirectUrl()
        ];

        return json_encode($buttonData);
    }
}
