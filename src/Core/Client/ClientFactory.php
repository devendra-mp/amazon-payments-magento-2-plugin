<?php

namespace Amazon\Core\Client;

use Amazon\Core\Model\EnvironmentChecker;
use Amazon\Payment\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use ReflectionClass;

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

        $client = $this->objectManager->create($this->instanceName, ['config' => $config]);

        if ($this->environmentChecker->isTestMode()) {
            $this->setTestEndpoints($client);
        }

        return $client;
    }

    protected function setTestEndpoints($client)
    {
        $reflection = new ReflectionClass($client);

        $mwsServiceUrls = $reflection->getProperty('mwsServiceUrls');
        $mwsServiceUrls->setAccessible(true);
        $mwsServiceUrls->setValue(
            $client,
            [
                'eu' => 'localhost:8000',
                'na' => 'localhost:8000',
                'jp' => 'localhost:8000'
            ]
        );

        $profileEndpointUrls = $reflection->getProperty('profileEndpointUrls');
        $profileEndpointUrls->setAccessible(true);
        $profileEndpointUrls->setValue(
            $client,
            [
                'uk' => 'localhost:8000',
                'us' => 'localhost:8000',
                'de' => 'localhost:8000',
                'jp' => 'localhost:8000'
            ]
        );
    }
}