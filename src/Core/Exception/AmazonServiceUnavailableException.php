<?php

namespace Amazon\Core\Exception;

use Magento\Framework\Exception\RemoteServiceUnavailableException;

class AmazonServiceUnavailableException extends RemoteServiceUnavailableException
{
    const ERROR_MESSAGE = 'Amazon could not process your request.';

    public function __construct()
    {
        parent::__construct(__(static::ERROR_MESSAGE));
    }
}