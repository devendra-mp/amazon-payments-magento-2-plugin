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
}