<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Domain\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

class AmazonCaptureResponse
{
    /**
     * @var string|null
     */
    protected $transactionId;

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
     * Get transaction id
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }
}