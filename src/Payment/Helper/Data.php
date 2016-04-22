<?php

namespace Amazon\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
    const MODULE_CODE = 'Amazon_Payment';

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * Data constructor.
     *
     * @param Context             $context
     * @param ModuleListInterface $moduleList
     */
    public function __construct(Context $context, ModuleListInterface $moduleList)
    {
        parent::__construct($context);
        $this->moduleList = $moduleList;
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->moduleList->getOne(static::MODULE_CODE)['setup_version'];
    }
}