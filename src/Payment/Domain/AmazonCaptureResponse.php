<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

class AmazonCaptureResponse
{
    /**
     * @var AmazonCaptureStatus
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $transactionId;

    /**
     * AmazonCaptureResponse constructor.
     *
     * @param ResponseInterface $reponse
     */
    public function __construct(ResponseInterface $reponse)
    {
        $data = $reponse->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['CaptureResult']['CaptureDetails'];

        $status       = $details['CaptureStatus'];
        $this->status = new AmazonCaptureStatus(
            $status['State'],
            (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        );

        if (isset($details['AmazonCaptureId'])) {
            $this->transactionId = $details['AmazonCaptureId'];
        }
    }

    /**
     * Get status
     *
     * @return AmazonCaptureStatus
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