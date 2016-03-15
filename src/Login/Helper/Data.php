<?php

namespace Amazon\Login\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /*
     * @return bool
     */
    public function getLoginEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool) $this->scopeConfig->getValue(
            'checkout/amazon_login/active',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonType($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'checkout/amazon_login/button/type',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonColor($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'checkout/amazon_login/button/color',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonSize($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'checkout/amazon_login/button/size',
            $scope
        );
    }
}