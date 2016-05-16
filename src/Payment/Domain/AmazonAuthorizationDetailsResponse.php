<?php

namespace Amazon\Payment\Domain;

class AmazonAuthorizationDetailsResponse extends AbstractAmazonAuthorizationResponse
{
    protected $resultKey = 'GetAuthorizationDetailsResult';

    /**
     * {@inheritDoc}
     */
    protected function getResultKey()
    {
        return $this->resultKey;
    }
}