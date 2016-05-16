<?php

namespace Amazon\Payment\Domain;

class AmazonAuthorizationStatus extends AbstractAmazonStatus
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
    const REASON_SELLER_CLOSED = 'SellerClosed';
    const REASON_EXPIRED_UNUSED = 'ExpiredUnused';

    const CODE_HARD_DECLINE = 4273;
    const CODE_SOFT_DECLINE = 7638;
}