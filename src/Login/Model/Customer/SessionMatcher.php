<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Customer\SessionMatcherInterface;
use Magento\Customer\Model\Session;

class SessionMatcher implements SessionMatcherInterface
{
    /**
     * @var Session
     */
    protected $session;

    public function __construct(
        Session $session
    )
    {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function match(AmazonCustomer $amazonCustomer)
    {
        if ($this->session->isLoggedIn()) {
            return $this->session->getCustomerData();
        }

        return null;
    }
}