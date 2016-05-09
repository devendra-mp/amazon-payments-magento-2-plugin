<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

abstract class AbstractAmazonCaptureResponse
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
     * AbstractAmazonCaptureResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, AmazonCaptureStatusFactory $amazonCaptureStatusFactory)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data[$this->getResultKey()]['CaptureDetails'];

        $status       = $details['CaptureStatus'];
        $this->status = $amazonCaptureStatusFactory->create([
            'state'      => $status['State'],
            'reasonCode' => (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        ]);

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

    /**
     * Get result key
     *
     * @return string
     */
    abstract protected function getResultKey();
}