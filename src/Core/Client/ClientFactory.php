<?php

namespace Amazon\Core\Client;

use Amazon\Core\Helper\Data;
use Amazon\Core\Model\EnvironmentChecker;
use Magento\Framework\ObjectManagerInterface;

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
     * @param Data                   $coreHelper
     * @param string                 $instanceName
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
    public function create()
    {
        $config = [
            'secret_key'  => $this->coreHelper->getSecretKey(),
            'access_key'  => $this->coreHelper->getAccessKey(),
            'merchant_id' => $this->coreHelper->getMerchantId(),
            'region'      => $this->coreHelper->getRegion(),
            'sandbox'     => $this->coreHelper->isSandboxEnabled(),
            'client_id'   => $this->coreHelper->getClientId()
        ];

        if ($this->environmentChecker->isTestMode()) {
            return new Mock($config);
        } else {
            return $this->objectManager->create($this->instanceName, ['config' => $config]);
        }
    }
}