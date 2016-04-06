<?php

namespace Amazon\Core\Client;

use Amazon\Core\Helper\Data;
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
     * ClientFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Data                   $coreHelper
     * @param string                 $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $coreHelper,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->coreHelper = $coreHelper;
        $this->instanceName  = $instanceName;
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

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}