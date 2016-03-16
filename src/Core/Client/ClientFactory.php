<?php

namespace Amazon\Core\Client;

use Magento\Framework\ObjectManagerInterface;
use Amazon\Payment\Helper\Data;

class ClientFactory implements ClientFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Data
     */
    protected $paymentHelper;

    /**
     * @var string
     */
    protected $instanceName;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $paymentHelper,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->paymentHelper = $paymentHelper;
        $this->instanceName  = $instanceName;
    }

    public function create()
    {
        $config = [
            'secret_key' => $this->paymentHelper->getClientSecret(),
            'access_key' => $this->paymentHelper->getAccessKey(),
            'region'     => $this->paymentHelper->getRegion(),
            'sandbox'    => $this->paymentHelper->getSandboxEnabled(),
            'client_id'  => $this->paymentHelper->getClientId()
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}