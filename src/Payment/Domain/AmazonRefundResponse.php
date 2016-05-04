<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

class AmazonRefundResponse
{
    /**
     * @var AmazonRefundStatus
     */
    protected $status;

    /**
     * AmazonRefundResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['RefundResult']['RefundDetails'];

        $status       = $details['RefundStatus'];
        $this->status = new AmazonRefundStatus(
            $status['State'],
            (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        );
    }

    /**
     * Get status
     *
     * @return AmazonRefundStatus
     */
    public function getStatus()
    {
        return $this->status;
    }
}