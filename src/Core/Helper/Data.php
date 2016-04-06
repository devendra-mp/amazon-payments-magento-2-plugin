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
            'payment/amazon_payment/general/credentials/merchant_id',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAccessKey($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/credentials/access_key',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getSecretKey($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/credentials/secret_key',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getClientId($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/credentials/client_id',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getClientSecret($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/credentials/client_secret',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getPaymentRegion($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/credentials/payment_region',
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
            'payment/amazon_payment/general/credentials/sandbox',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isPwaEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/general/options/pwa_enabled',
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
            'payment/amazon_payment/general/options/lwa_enabled',
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
            'payment/amazon_payment/general/options/payment_action',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAuthorizationMode($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/options/authorization_mode',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getUpdateMechanism($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/general/options/update_mechanism',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getDisplayLanguage($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/frontend/display_language',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAuthenticationExperience($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/frontend/authentication_experience',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonType($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/frontend/button_type',
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
            'payment/amazon_payment/advanced/frontend/button_color',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getButtonSize($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/frontend/button_size',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getNewOrderStatus($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/sales_options/new_order_status',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAuthorizedOrderStatus($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/sales_options/authorized_order_status',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getCapturedOrderStatus($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/sales_options/captured_order_status',
            $scope
        );
    }


    /*
     * @return string
     */
    public function getEmailStoreName($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/sales_options/email_store_name',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getAdditionalAccessScope($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/sales_options/additional_access_scope',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isExcludePackingStations($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/sales_exclusions/exclude_packing_stations',
            $scope
        );
    }

    /*
     * @return bool
     */
    public function isLoggingEnabled($scope = ScopeInterface::SCOPE_STORE)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/developer_options/logging',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getRestrictedIps($scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/advanced/developer_options/restricted_ips',
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