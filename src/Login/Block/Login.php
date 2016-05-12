<?php

namespace Amazon\Login\Block;

use Amazon\Core\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Login extends Template
{
    /**
     * @var Data
     */
    protected $coreHelper;

    public function __construct(Context $context, Data $coreHelper)
    {
        $this->coreHelper = $coreHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isLwaEnabled()
    {
        return (
            $this->coreHelper->isLwaEnabled()
            && $this->coreHelper->isPwaEnabled()
            && $this->coreHelper->getCurrencyCode() == $this->getCurrentCurrencyCode()
        );
    }

    protected function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}
