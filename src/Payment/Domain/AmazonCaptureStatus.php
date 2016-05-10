<?php

namespace Amazon\Payment\Domain;

class AmazonCaptureStatus extends AbstractAmazonStatus
{
    const STATE_COMPLETED = 'Completed';
    const STATE_PENDING = 'Pending';
    const STATE_DECLINED = 'Declined';
    const STATE_CLOSED = 'Closed';
}