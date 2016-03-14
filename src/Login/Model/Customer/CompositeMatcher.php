<?php

namespace Amazon\Login\Model\Customer;

use Amazon\Core\Domain\AmazonCustomer;
use Amazon\Login\Api\Data\Customer\CompositeMatcherInterface;
use Amazon\Login\Api\Data\Customer\MatcherInterface;

class CompositeMatcher implements CompositeMatcherInterface
{
    /**
     * @var MatcherInterface[]
     */
    protected $matchers;

    public function __construct(array $matchers)
    {
        $this->matchers = $matchers;
    }

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