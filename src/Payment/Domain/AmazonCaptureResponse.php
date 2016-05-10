<?php

namespace Amazon\Payment\Domain;

class AmazonCaptureResponse extends AbstractAmazonCaptureResponse
{
    protected $resultKey = 'CaptureResult';

    /**
     * {@inheritDoc}
     */
    protected function getResultKey()
    {
       return $this->resultKey;
    }
}