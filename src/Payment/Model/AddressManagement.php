<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Payment\Api\AddressManagementInterface;
use Amazon\Payment\Helper\Address;
use Magento\Checkout\Model\Session;
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

    /**
     * @var Session
     */
    protected $session;

    public function __construct(ClientFactoryInterface $clientFactory, Address $address, Session $session)
    {
        $this->clientFactory = $clientFactory;
        $this->address       = $address;
        $this->session       = $session;
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

        return $this->address->convertToArray($address);
    }
}