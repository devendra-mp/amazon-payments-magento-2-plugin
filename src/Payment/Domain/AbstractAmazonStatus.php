<?php

namespace Amazon\Payment\Domain;

abstract class AbstractAmazonStatus
{
    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $reasonCode;

    /**
     * AmazonAuthorizationStatus constructor.
     *
     * @param string $state
     * @param string|null $reasonCode
     */
    public function __construct($state, $reasonCode = null)
    {
        $this->state      = $state;
        $this->reasonCode = $reasonCode;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get reason code
     *
     * @return string|null
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }
}