<?php

namespace Context\Data;

use Behat\Behat\Context\SnippetAcceptingContext;

class AmazonContext implements SnippetAcceptingContext
{
    /**
     * @Then my amazon order should be cancelled
     */
    public function myAmazonOrderShouldBeCancelled()
    {
        throw new PendingException();
    }
}