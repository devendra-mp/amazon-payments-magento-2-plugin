<?php

namespace Amazon\Login\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Validate extends Template
{
    public function getForgotPasswordUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/forgotpassword');
    }

    public function getContinueAsGuestUrl()
    {
        return $this->_urlBuilder->getUrl('checkout');
    }
}