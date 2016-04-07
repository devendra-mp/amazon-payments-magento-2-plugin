<?php

namespace Amazon\Login\Block;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Validate extends Template
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    public function getForgotPasswordUrl()
    {
        return $this->urlBuilder->getUrl('customer/account/forgotpassword');
    }

    public function getContinueAsGuestUrl()
    {
        return $this->urlBuilder->getUrl('checkout');
    }
}