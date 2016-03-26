<?php

namespace Amazon\Payment\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Amazon\Payment\Helper\Data;

class Link extends Template
{
    /**
     * @var Data
     */
    private $paymentHelper;

    public function __construct(Context $context, Data $paymentHelper) {
        $this->paymentHelper = $paymentHelper;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->paymentHelper->isEnabled();
    }
}
