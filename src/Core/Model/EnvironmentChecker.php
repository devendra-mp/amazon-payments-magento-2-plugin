<?php

namespace Amazon\Core\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class EnvironmentChecker
{
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function isTestMode()
    {
        return ('1' === $this->config->getValue('is_behat_running'));
    }
}