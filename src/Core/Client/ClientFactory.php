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
            'secret_key'          => 'emjDblxKihA2BJw8zzsya0cELzzE0AHAk/Dms9Ir',
            'access_key'          => 'AKIAJW4QNZTWAI7TB5OA',
            'region'              => 'uk',
            'sandbox'             => true,
            'client_id'           => 'amzn1.application-oa2-client.fe5d817cfb2b45dcaf1c2c15966454bb'
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}