<?php

namespace Amazon\Core\Exception;

use Magento\Framework\Exception\RemoteServiceUnavailableException;
use Magento\Framework\Phrase;

class AmazonServiceUnavailableException extends RemoteServiceUnavailableException
{
    public function __construct()
    {
        parent::__construct(new Phrase('Amazon could not process your request.'));
    }
}