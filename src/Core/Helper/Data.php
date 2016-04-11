<?php

namespace Amazon\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /*
     * @return string
     */
    public function getMerchantId($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/merchant_id',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAccessKey($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/access_key',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getSecretKey($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/secret_key',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getClientId($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/client_id',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getClientSecret($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/client_secret',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getPaymentRegion($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/payment_region',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getRegion($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->getPaymentRegion($scope);
    }

    /*
     * @return string
     */
    public function getCurrencyCode($scope = ScopeInterface::SCOPE_STORE)
    {
        $paymentRegion = $this->getPaymentRegion($scope);

        $currencyCodeMap = [
            'de' => 'EUR',
            'uk' => 'GBP',
            'us' => 'USD',
            'jp' => 'YEN',
        ];

        return array_key_exists($paymentRegion, $currencyCodeMap) ? $currencyCodeMap[$paymentRegion]: '';
    }

    /*
     * @return bool
     */
    public function isSandboxEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/sandbox',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isPwaEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/pwa_enabled',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isLwaEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        $pwaEnabled = $this->isPwaEnabled($scope);
        $lwaEnabled = (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/lwa_enabled',
            $scope
        );

        return $pwaEnabled && $lwaEnabled;
    }

    /*
     * @return string
     */
    public function getPaymentAction($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/payment_action',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAuthorizationMode($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/authorization_mode',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getUpdateMechanism($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/update_mechanism',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getDisplayLanguage($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/display_language',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAuthenticationExperience($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/authentication_experience',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonType($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/button_type',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonTypePwa($scope = ScopeInterface::SCOPE_STORE)
    {
        $buttonType = $this->getButtonType($scope);

        $buttonTypeMap = [
            'full' => 'PwA',
            'short' => 'Pay',
            'logo' => 'A',
        ];

        return array_key_exists($buttonType, $buttonTypeMap) ? $buttonTypeMap[$buttonType]: '';
    }

    /*
     * @return string
     */
    public function getButtonTypeLwa($scope = ScopeInterface::SCOPE_STORE)
    {
        $buttonType = $this->getButtonType($scope);

        $buttonTypeMap = [
            'full' => 'LwA',
            'short' => 'Login',
            'logo' => 'A',
        ];

        return array_key_exists($buttonType, $buttonTypeMap) ? $buttonTypeMap[$buttonType]: '';
    }

    /*
     * @return string
     */
    public function getButtonColor($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/button_color',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonSize($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/button_size',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getNewOrderStatus($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/new_order_status',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAuthorizedOrderStatus($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/authorized_order_status',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getCapturedOrderStatus($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/captured_order_status',
            $scope
        );
    }


    /*
     * @return string
     */
    public function getEmailStoreName($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/email_store_name',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAdditionalAccessScope($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/additional_access_scope',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isExcludePackingStations($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/exclude_packing_stations',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isLoggingEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/logging',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getRestrictedIps($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/restricted_ips',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_getUrl('amazon/login/authorize', ['_secure' => true]);
    }
}