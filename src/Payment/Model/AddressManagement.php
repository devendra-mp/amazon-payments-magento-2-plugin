<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Payment\Api\AddressManagementInterface;
use Amazon\Payment\Helper\Address;
use PayWithAmazon\ResponseInterface;

class AddressManagement implements AddressManagementInterface
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var Address
     */
    protected $address;

    public function __construct(ClientFactoryInterface $clientFactory, Address $address)
    {
        $this->clientFactory = $clientFactory;
        $this->address       = $address;
    }

    /**
     * {@inheritDoc}
     */
    public function saveShippingAddress($amazonOrderReferenceId, $addressConsentToken)
    {
        $client = $this->clientFactory->create();

        /**
         * @var ResponseInterface $response
         */
        $response = $client->getOrderReferenceDetails(
            [
                'amazon_order_reference_id' => $amazonOrderReferenceId,
                'address_consent_token'     => $addressConsentToken
            ]
        );

        $amazonAddress = new AmazonAddress($response);
        $address       = $this->address->convertToMagentoEntity($amazonAddress);

        return [$this->address->convertToArray($address)];
    }
}