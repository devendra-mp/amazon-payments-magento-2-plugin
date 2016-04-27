<?php

namespace Amazon\Core\Exception;

use Magento\Framework\Exception\RemoteServiceUnavailableException;
use Magento\Framework\Phrase;

class AmazonServiceUnavailableException extends RemoteServiceUnavailableException
{
    const ERROR_MESSAGE = 'Amazon could not process your request.';

    public function __construct()
    {
        parent::__construct(new Phrase(static::ERROR_MESSAGE));
    }
}