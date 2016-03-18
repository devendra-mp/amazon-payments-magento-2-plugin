<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Customer\CompositeMatcherInterface;
use Amazon\Login\Api\Customer\MatcherInterface;

class CompositeMatcher implements CompositeMatcherInterface
{
    /**
     * @var MatcherInterface[]
     */
    protected $matchers;

    /**
     * CompositeMatcher constructor.
     *
     * @param array $matchers
     */
    public function __construct(array $matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * {@inheritDoc}
     */
    public function match(AmazonCustomer $amazonCustomer)
    {
        foreach ($this->matchers as $matcher) {
            if ($customerData = $matcher->match($amazonCustomer)) {
                return $customerData;
            }
        }

        return null;
    }
}