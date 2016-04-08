<?php

namespace Amazon\Payment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Amazon\Core\Helper\Data;

class Link extends Template
{
    /**
     * @var Data
     */
    protected $coreHelper;

    public function __construct(Context $context, Data $coreHelper) {
        $this->coreHelper = $coreHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isPwaEnabled()
    {
        return $this->coreHelper->isPwaEnabled();
    }
}
