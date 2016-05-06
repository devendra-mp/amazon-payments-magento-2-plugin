<?php

namespace Amazon\Payment\Domain;

class AmazonCaptureDetailsResponse extends AbstractAmazonCaptureResponse
{
    protected $resultKey = 'GetCaptureDetailsResult';

    /**
     * {@inheritDoc}
     */
    protected function getResultKey()
    {
        return $this->resultKey;
    }
}