<?php

namespace Amazon\Payment\Domain;

class AmazonAuthorizationDetailsResponse extends AbstractAmazonAuthorizationResponse
{
    protected $resultKey = 'AuthorizeDetailsResult';

    /**
     * {@inheritDoc}
     */
    protected function getResultKey()
    {
        return $this->resultKey;
    }
}