<?php
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    protected $amazonAccountUrl = [
        'us' => 'https://payments.amazon.com/overview',
        'uk' => 'https://payments.amazon.co.uk/overview',
        'de' => 'https://payments.amazon.de/overview',
        'jp' => 'https://payments.amazon.co.jp/overview',
    ];

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    private $remoteIp;

    /**
     * @param Context               $context
     * @param EncryptorInterface    $encryptor
     * @param StoreManagerInterface $storeManager
     * @param RemoteAddress         $remoteAddress
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);
        $this->encryptor    = $encryptor;
        $this->storeManager = $storeManager;
        $this->remoteIp     = $remoteAddress->getRemoteAddress();
    }

    /*
     * @return string
     */
    public function getMerchantId($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/merchant_id',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getAccessKey($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/access_key',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getSecretKey($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $secretKey = $this->scopeConfig->getValue(
            'payment/amazon_payment/secret_key',
            $scope,
            $scopeCode
        );
        $secretKey = $this->encryptor->decrypt($secretKey);

        return $secretKey;
    }

    /*
     * @return string
     */
    public function getClientId($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/client_id',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getClientSecret($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $clientSecret = $this->scopeConfig->getValue(
            'payment/amazon_payment/client_secret',
            $scope,
            $scopeCode
        );
        $clientSecret = $this->encryptor->decrypt($clientSecret);

        return $clientSecret;
    }

    /*
     * @return string
     */
    public function getPaymentRegion($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/payment_region',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getRegion($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->getPaymentRegion($scope, $scopeCode);
    }

    /*
     * @return string
     */
    public function getCurrencyCode($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $paymentRegion = $this->getPaymentRegion($scope, $scopeCode);

        $currencyCodeMap = [
            'de' => 'EUR',
            'uk' => 'GBP',
            'us' => 'USD',
            'jp' => 'JPY'
        ];

        return array_key_exists($paymentRegion, $currencyCodeMap) ? $currencyCodeMap[$paymentRegion]: '';
    }

    /*
     * @return string
     */
    public function getWidgetUrl($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $paymentRegion = $this->getPaymentRegion($scope, $scopeCode);
        $sandboxEnabled = $this->isSandboxEnabled($scope, $scopeCode);

        $widgetUrlMap = [
            'de' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js',
            'uk' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/lpa/js/Widgets.js',
            'us' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/js/Widgets.js',
            'jp' => 'https://origin-na.ssl-images-amazon.com/images/G/09/EP/offAmazonPayments/sandbox/prod/lpa/js/Widgets.js',
        ];

        if ($sandboxEnabled) {
            $widgetUrlMap = [
                'de' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js',
                'uk' => 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js',
                'us' => 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js',
                'jp' => 'https://origin-na.ssl-images-amazon.com/images/G/09/EP/offAmazonPayments/sandbox/prod/lpa/js/Widgets.js',
            ];
        }

        return array_key_exists($paymentRegion, $widgetUrlMap) ? $widgetUrlMap[$paymentRegion]: '';
    }

    /**
     * @param string $scope
     *
     * @return string
     */
    public function getLoginScope($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $paymentRegion = $this->getPaymentRegion($scope, $scopeCode);

        $scope = [
            'profile',
            'payments:widget',
            'payments:shipping_address',
        ];

        if (in_array($paymentRegion, ['uk', 'de', 'jp'])) {
            $scope[] = 'payments:billing_address';
        }

        return implode(' ', $scope);
    }

    /**
     * @param string $scope
     *
     * @return boolean
     */
    public function isEuPaymentRegion($scope = ScopeInterface::SCOPE_STORE)
    {
        $paymentRegion = $this->getPaymentRegion($scope);

        return (in_array($paymentRegion, ['uk', 'de']));
    }

    /*
     * @return bool
     */
    public function isSandboxEnabled($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/sandbox',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return bool
     */
    public function isPwaEnabled($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        if ( ! $this->clientHasAllowedIp()) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            'payment/amazon_payment/pwa_enabled',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return bool
     */
    public function isLwaEnabled($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        if ( ! $this->clientHasAllowedIp()) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            'payment/amazon_payment/lwa_enabled',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getPaymentAction($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/payment_action',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getAuthorizationMode($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/authorization_mode',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getUpdateMechanism($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/update_mechanism',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getDisplayLanguage($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/display_language',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getAuthenticationExperience($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/authentication_experience',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getButtonType($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/button_type',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getButtonTypePwa($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $buttonType = $this->getButtonType($scope, $scopeCode);

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
    public function getButtonTypeLwa($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $buttonType = $this->getButtonType($scope, $scopeCode);

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
    public function getButtonColor($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/button_color',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getButtonSize($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/button_size',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getNewOrderStatus($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/new_order_status',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getAuthorizedOrderStatus($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/authorized_order_status',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getCapturedOrderStatus($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/captured_order_status',
            $scope,
            $scopeCode
        );
    }


    /*
     * @return string
     */
    public function getEmailStoreName($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/email_store_name',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string
     */
    public function getAdditionalAccessScope($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            'payment/amazon_payment/additional_access_scope',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return bool
     */
    public function isExcludePackingStations($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/exclude_packing_stations',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return bool
     */
    public function isLoggingEnabled($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return (bool)$this->scopeConfig->getValue(
            'payment/amazon_payment/logging',
            $scope,
            $scopeCode
        );
    }

    /*
     * @return string[]
     */
    public function getAllowedIps($scope = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $allowedIpsString = $this->scopeConfig->getValue('payment/amazon_payment/allowed_ips', $scope, $scopeCode);
        return empty($allowedIpsString) ? [] : explode(',', $allowedIpsString);
    }

    /**
     * @return bool
     */
    public function clientHasAllowedIp()
    {
        $allowedIps = $this->getAllowedIps();

        return empty($allowedIps) ? true : in_array($this->remoteIp, $allowedIps);
    }

    /*
     * @return string
     */
    public function getRedirectUrl()
    {
        $urlPath = $this->isLwaEnabled() ? 'amazon/login/authorize' : 'amazon/login/guest';
        return $this->_getUrl($urlPath, ['_secure' => true]);
    }

    /**
     * @param string|null $context
     * @return array
     */
    public function getSandboxSimulationStrings($context = null)
    {
        $simulationStrings = [
            'default' => null
        ];

        if (in_array($context, ['authorization', 'authorization_capture'])) {
            $simulationStrings['Authorization:Declined:InvalidPaymentMethod']
                = '{"SandboxSimulation": {"State":"Declined", "ReasonCode":"InvalidPaymentMethod"}}';
            $simulationStrings['Authorization:Declined:AmazonRejected']
                = '{"SandboxSimulation": {"State":"Declined", "ReasonCode":"AmazonRejected"}}';
            $simulationStrings['Authorization:Declined:TransactionTimedOut']
                = '{"SandboxSimulation": {"State":"Declined", "ReasonCode":"TransactionTimedOut"}}';
        }

        if (in_array($context, ['capture', 'authorization_capture'])) {
            $simulationStrings['Capture:Declined:AmazonRejected']
                = '{"SandboxSimulation": {"State":"Declined", "ReasonCode":"AmazonRejected"}}';
            $simulationStrings['Capture:Pending']
                = '{"SandboxSimulation": {"State":"Pending"}}';
        }

        return $simulationStrings;
    }

    /**
     * @return array
     */
    public function getSandboxSimulationOptions()
    {
        $simulationlabels = [
            'default' => 'No Simulation',
            'Authorization:Declined:InvalidPaymentMethod' => 'Authorization soft decline',
            'Authorization:Declined:AmazonRejected' => 'Authorization hard decline',
            'Authorization:Declined:TransactionTimedOut' => 'Authorization timed out',
            'Capture:Declined:AmazonRejected' => 'Capture declined',
            'Capture:Pending' => 'Capture pending'
        ];

        return $simulationlabels;
    }

    public function isPaymentButtonEnabled()
    {
        return ($this->isPwaEnabled() && $this->isCurrentCurrencySupportedByAmazon());
    }

    public function isLoginButtonEnabled()
    {
        return ($this->isLwaEnabled() && $this->isPwaEnabled() && $this->isCurrentCurrencySupportedByAmazon());
    }

    public function isCurrentCurrencySupportedByAmazon()
    {
        return $this->getCurrentCurrencyCode() == $this->getCurrencyCode();
    }

    protected function getCurrentCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * @param string $paymentRegion E.g. "uk", "us", "de", "jp".
     *
     * @return mixed
     */
    public function getAmazonAccountUrlByPaymentRegion($paymentRegion)
    {
        if (empty($this->amazonAccountUrl[$paymentRegion])) {
            throw new \InvalidArgumentException("$paymentRegion is not a valid payment region");
        }

        return $this->amazonAccountUrl[$paymentRegion];
    }
}
