<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Exception\AmazonServiceUnavailableException;
use PayWithAmazon\ResponseInterface;

class AmazonAuthorizationResponse
{
    /**
     * @var AmazonAuthorizationStatus
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $captureTransactionId;

    /**
     * @var string|null
     */
    protected $authorizeTransactionId;

    /**
     * AmazonAuthorizationResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }

        $details = $data['AuthorizeResult']['AuthorizationDetails'];

        $status       = $details['AuthorizationStatus'];
        $this->status = new AmazonAuthorizationStatus(
            $status['State'],
            (isset($status['ReasonCode']) ? $status['ReasonCode'] : null)
        );

        if (isset($details['IdList']['member'])) {
            $this->captureTransactionId = $details['IdList']['member'];
        }

        if (isset($details['AmazonAuthorizationId'])) {
            $this->authorizeTransactionId = $details['AmazonAuthorizationId'];
        }
    }

    /**
     * Get status
     *
     * @return AmazonAuthorizationStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get authorize transaction id
     *
     * @return string|null
     */
    public function getAuthorizeTransactionId()
    {
        return $this->authorizeTransactionId;
    }

    /**
     * Get capture transaction id
     *
     * @return string|null
     */
    public function getCaptureTransactionId()
    {
        return $this->captureTransactionId;
    }
}