<?php

namespace Amazon\Core\Client;

use Amazon\Core\Helper\Data;
use Amazon\Core\Model\EnvironmentChecker;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Data
     */
    protected $coreHelper;

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * @var EnvironmentChecker
     */
    protected $environmentChecker;

    /**
     * ClientFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Data $coreHelper
     * @param EnvironmentChecker $environmentChecker
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $coreHelper,
        EnvironmentChecker $environmentChecker,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager      = $objectManager;
        $this->coreHelper         = $coreHelper;
        $this->environmentChecker = $environmentChecker;
        $this->instanceName       = $instanceName;
    }

    /**
     * {@inheritDoc}
     */
    public function create($storeId = null)
    {
        $config = [
            'secret_key'  => $this->coreHelper->getSecretKey(ScopeInterface::SCOPE_STORE, $storeId),
            'access_key'  => $this->coreHelper->getAccessKey(ScopeInterface::SCOPE_STORE, $storeId),
            'merchant_id' => $this->coreHelper->getMerchantId(ScopeInterface::SCOPE_STORE, $storeId),
            'region'      => $this->coreHelper->getRegion(ScopeInterface::SCOPE_STORE, $storeId),
            'sandbox'     => $this->coreHelper->isSandboxEnabled(ScopeInterface::SCOPE_STORE, $storeId),
            'client_id'   => $this->coreHelper->getClientId(ScopeInterface::SCOPE_STORE, $storeId)
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}
