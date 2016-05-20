<?php

namespace Fixtures;

use Amazon\Core\Client\ClientFactoryInterface;
use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Quote\Api\CartRepositoryInterface;

class AmazonOrder extends BaseFixture
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    public function __construct()
    {
        parent::__construct();
        $this->clientFactory = $this->getMagentoObject(ClientFactoryInterface::class);
    }

    public function getState($orderRef)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getOrderReferenceDetails(
            ['amazon_order_reference_id' => $orderRef]
        );

        $data = $response->toArray();

        return $data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['OrderReferenceStatus']['State'];
    }

    public function getAuthrorizationState($authorizationId)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getAuthorizationDetails(
            [
                'amazon_authorization_id' => $authorizationId
            ]
        );

        $data = $response->toArray();

        return $data['GetAuthorizationDetailsResult']['AuthorizationDetails']['AuthorizationStatus']['State'];
    }


    public function getCaptureState($captureId)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getCaptureDetails(
            [
                'amazon_capture_id' => $captureId
            ]
        );

        $data = $response->toArray();

        return $data['GetCaptureDetailsResult']['CaptureDetails']['CaptureStatus']['State'];
    }
}