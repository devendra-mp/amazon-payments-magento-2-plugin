<?php

namespace Context\Web\Admin;

use Behat\Behat\Context\SnippetAcceptingContext;
use PHPUnit_Framework_Assert;

class OrderContext implements SnippetAcceptingContext
{
    /**
     * @Given I go to invoice the last order for :arg1
     */
    public function iGoToInvoiceTheLastOrderFor($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given I submit my invoice
     */
    public function iSubmitMyInvoice()
    {
        throw new PendingException();
    }
}