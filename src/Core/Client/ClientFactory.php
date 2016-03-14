<?php

namespace Amazon\Core\Client;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $instanceName;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->config        = $scopeConfig;
        $this->objectManager = $objectManager;
        $this->instanceName  = $instanceName;
    }

    public function create()
    {
        $config = [
            'secret_key' => $this->config->getValue(
                'payment/amazon_payment/secret_key',
                ScopeInterface::SCOPE_STORE
            ),
            'access_key' => $this->config->getValue(
                'payment/amazon_payment/access_key',
                ScopeInterface::SCOPE_STORE
            ),
            'region'     => $this->config->getValue(
                'payment/amazon_payment/region',
                ScopeInterface::SCOPE_STORE
            ),
            'sandbox'    => (bool)$this->config->getValue(
                'payment/amazon_payment/sandbox',
                ScopeInterface::SCOPE_STORE
            ),
            'client_id'  => $this->config->getValue(
                'payment/amazon_payment/client_id',
                ScopeInterface::SCOPE_STORE
            )
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}