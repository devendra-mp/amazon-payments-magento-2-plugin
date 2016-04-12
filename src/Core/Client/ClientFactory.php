<?php

namespace Amazon\Core\Client;

use Amazon\Core\Model\EnvironmentChecker;
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
     * @var EnvironmentChecker
     */
    protected $environmentChecker;

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
        EnvironmentChecker $environmentChecker,
        $instanceName = '\\PayWithAmazon\\ClientInterface'
    ) {
        $this->objectManager      = $objectManager;
        $this->paymentHelper      = $paymentHelper;
        $this->environmentChecker = $environmentChecker;
        $this->instanceName       = $instanceName;
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

        if ($this->environmentChecker->isTestMode()) {
            return new Mock($config);
        } else {
            return $this->objectManager->create($this->instanceName, ['config' => $config]);
        }
    }
}