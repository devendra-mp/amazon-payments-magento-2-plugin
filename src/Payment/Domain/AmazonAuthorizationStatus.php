<?php

namespace Amazon\Payment\Domain;

class AmazonAuthorizationStatus
{
    const STATE_OPEN = 'Open';
    const STATE_PENDING = 'Pending';
    const STATE_DECLINED = 'Declined';
    const STATE_CLOSED = 'Closed';

    const REASON_INVALID_PAYMENT_METHOD = 'InvalidPaymentMethod';
    const REASON_PROCESSING_FAILURE = 'ProcessingFailure';
    const REASON_AMAZON_REJECTED = 'AmazonRejected';
    const REASON_TRANSACTION_TIMEOUT = 'TransactionTimedOut';
    const REASON_MAX_CAPTURES_PROCESSED = 'MaxCapturesProcessed';

    const CODE_HARD_DECLINE = 4273;
    const CODE_SOFT_DECLINE = 7638;

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