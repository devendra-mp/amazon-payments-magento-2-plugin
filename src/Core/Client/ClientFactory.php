<?php

namespace Amazon\Core\Client;

use Amazon\Payment\Helper\Data;
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
    protected $paymentHelper;

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * ClientFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Data                   $paymentHelper
     * @param string                 $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $paymentHelper,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->paymentHelper = $paymentHelper;
        $this->instanceName  = $instanceName;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $config = [
            'secret_key'  => $this->paymentHelper->getSecretKey(),
            'access_key'  => $this->paymentHelper->getAccessKey(),
            'merchant_id' => $this->paymentHelper->getMerchantId(),
            'region'      => $this->paymentHelper->getRegion(),
            'sandbox'     => $this->paymentHelper->isSandboxEnabled(),
            'client_id'   => $this->paymentHelper->getClientId()
        ];

        return $this->objectManager->create($this->instanceName, ['config' => $config]);
    }
}