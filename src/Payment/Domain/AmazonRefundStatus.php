<?php

namespace Amazon\Payment\Domain;

class AmazonRefundStatus extends AbstractAmazonStatus
{
    const STATE_PENDING = 'Pending';
    const STATE_COMPLETED = 'Completed';
    const STATE_DECLINED = 'Declined';
}