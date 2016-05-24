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
     * @var string|null
     */
    protected $transactionId;

    /**
     * AmazonRefundResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, AmazonRefundStatusFactory $amazonRefundStatusFactory)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['RefundResult']['RefundDetails'];

        $status       = $details['RefundStatus'];
        $this->status = $amazonRefundStatusFactory->create([
            'state'      => $status['State'],
            'reasonCode' => (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        ]);

        if (isset($details['AmazonRefundId'])) {
            $this->transactionId = $details['AmazonRefundId'];
        }
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

    /**
     * Get transaction id
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}