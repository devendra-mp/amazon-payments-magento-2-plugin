<?php

namespace Amazon\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
    const MODULE_CODE = 'Amazon_Payment';

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->moduleList->getOne(static::MODULE_CODE)['setup_version'];
    }
}