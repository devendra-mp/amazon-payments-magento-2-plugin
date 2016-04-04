<?php

namespace Amazon\Login\Plugin;

use Magento\Checkout\Controller\Cart\Index;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\Page;

class CartController
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(Session $session, UrlInterface $urlBuilder)
    {
        $this->session    = $session;
        $this->urlBuilder = $urlBuilder;
    }

    public function afterExecute(Index $index, Page $page)
    {
        $this->session->setBeforeAuthUrl($this->urlBuilder->getUrl('checkout'));

        return $page;
    }
}