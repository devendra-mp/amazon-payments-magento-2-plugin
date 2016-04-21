<?php

namespace Amazon\Core\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class EnvironmentChecker
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * EnvironmentChecker constructor.
     *
     * @param ScopeConfigInterface $config
     */
    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Check if behat is running
     *
     * @return bool
     */
    public function isTestMode()
    {
        return ('1' === $this->config->getValue('is_behat_running'));
    }
}