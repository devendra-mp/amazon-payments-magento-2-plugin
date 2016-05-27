<?php

namespace Fixtures;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddressFactory;
use Amazon\Payment\Helper\Address;
use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Quote\Api\CartRepositoryInterface;

class AmazonOrder extends BaseFixture
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var AmazonAddressFactory
     */
    protected $amazonAddressFactory;

    /**
     * @var Address
     */
    protected $addressHelper;

    public function __construct()
    {
        parent::__construct();
        $this->clientFactory        = $this->getMagentoObject(ClientFactoryInterface::class);
        $this->amazonAddressFactory = $this->getMagentoObject(AmazonAddressFactory::class);
        $this->addressHelper        = $this->getMagentoObject(Address::class);
    }

    public function getShippingAddress($orderRef, $addressConsentToken)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getOrderReferenceDetails(
            [
                'amazon_order_reference_id' => $orderRef,
                'address_consent_token'     => $addressConsentToken
            ]
        );

        $data = $response->toArray();
        if (isset($data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination'])) {
            $address = $this->amazonAddressFactory->create(['address' => $data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination']]);
            return $this->addressHelper->convertToMagentoEntity($address);
        } else {
            throw new \Exception('failed to retrieve address data from Amazon');
        }
    }

    public function getBillingAddress($orderRef, $addressConsentToken)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getOrderReferenceDetails(
            [
                'amazon_order_reference_id' => $orderRef,
                'address_consent_token'     => $addressConsentToken
            ]
        );

        $data = $response->toArray();
        if (isset($data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['BillingAddress']['PhysicalAddress'])) {
            $address = $this->amazonAddressFactory->create(['address' => $data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['BillingAddress']['PhysicalAddress']]);
            return $this->addressHelper->convertToMagentoEntity($address);
        } else {
            throw new \Exception('failed to retrieve address data from Amazon');
        }
    }

    public function getState($orderRef)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getOrderReferenceDetails(
            ['amazon_order_reference_id' => $orderRef]
        );

        $data = $response->toArray();

        if (isset($data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['OrderReferenceStatus']['State'])) {
            return $data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['OrderReferenceStatus']['State'];
        } else {
            throw new \Exception('failed to retrieve order state from Amazon');
        }
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

        if (isset($data['GetAuthorizationDetailsResult']['AuthorizationDetails']['AuthorizationStatus']['State'])) {
            return $data['GetAuthorizationDetailsResult']['AuthorizationDetails']['AuthorizationStatus']['State'];
        } else {
            throw new \Exception('failed to retrieve authorization state from Amazon');
        }
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

        if (isset($data['GetCaptureDetailsResult']['CaptureDetails']['CaptureStatus']['State'])) {
            return $data['GetCaptureDetailsResult']['CaptureDetails']['CaptureStatus']['State'];
        } else {
            throw new \Exception('failed to retrieve capture state from Amazon');
        }
    }

    public function getRefundState($refundId)
    {
        $client   = $this->clientFactory->create();
        $response = $client->getRefundDetails(
            [
                'amazon_refund_id' => $refundId
            ]
        );

        $data = $response->toArray();

        if (isset($data['GetRefundDetailsResult']['RefundDetails']['RefundStatus']['State'])) {
            return $data['GetRefundDetailsResult']['RefundDetails']['RefundStatus']['State'];
        } else {
            throw new \Exception('failed to retrieve refund state from Amazon');
        }
    }
}