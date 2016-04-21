<?php

namespace Amazon\Payment\Domain;

use Amazon\Core\Domain\RemoteServiceUnavailableException;
use Magento\Framework\Phrase;
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
     * @param ResponseInterface $reponse
     */
    public function __construct(ResponseInterface $reponse)
    {
        $data = $reponse->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new RemoteServiceUnavailableException();
        }

        $details = $data['AuthorizeResult']['AuthorizationDetails'];

        $status = $details['AuthorizationStatus'];
        $this->status = new AmazonAuthorizationStatus($status['State'], $status['ReasonCode']);

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
     * @return null|string
     */
    public function getAuthorizeTransactionId()
    {
        return $this->authorizeTransactionId;
    }

    /**
     * Get capture transaction id
     * 
     * @return null|string
     */
    public function getCaptureTransactionId()
    {
        return $this->captureTransactionId;
    }
}