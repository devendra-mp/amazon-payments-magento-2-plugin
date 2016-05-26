<?php

namespace Amazon\Login\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class SetAuthorizeErrorCookie implements ObserverInterface
{
    const LOGIN_AUTHORIZE_ERROR_COOKIE = 'amz_auth_err';

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $cookieMeta = $this->cookieMetadataFactory
                           ->createPublicCookieMetadata()
                           ->setDurationOneYear()
                           ->setPath('/')
                           ->setHttpOnly(false); // JS-accessible

        $this->cookieManager->setPublicCookie(self::LOGIN_AUTHORIZE_ERROR_COOKIE, '1', $cookieMeta);
    }
}
