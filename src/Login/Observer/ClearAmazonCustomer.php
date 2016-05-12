<?php

namespace Amazon\Login\Observer;

use Amazon\Login\Helper\Session as SessionHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ClearAmazonCustomer implements ObserverInterface
{
    /**
     * @var SessionHelper
     */
    protected $sessionHelper;

    /**
     * @param SessionHelper $sessionHelper
     */
    public function __construct(SessionHelper $sessionHelper)
    {
        $this->sessionHelper = $sessionHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $this->sessionHelper->clearAmazonCustomer();
    }
}
