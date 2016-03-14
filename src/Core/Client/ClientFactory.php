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
    protected $config;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $instanceName;

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
                'payment/amazon_payment/client/secret_key',
                ScopeInterface::SCOPE_STORE
            ),
            'access_key' => $this->config->getValue(
                'payment/amazon_payment/client/access_key',
                ScopeInterface::SCOPE_STORE
            ),
            'region'     => $this->config->getValue(
                'payment/amazon_payment/client/region',
                ScopeInterface::SCOPE_STORE
            ),
            'sandbox'    => (bool)$this->config->getValue(
                'payment/amazon_payment/developer/sandbox',
                ScopeInterface::SCOPE_STORE
            ),
            'client_id'  => $this->config->getValue(
                'payment/amazon_payment/client/client_id',
                ScopeInterface::SCOPE_STORE
            )
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}