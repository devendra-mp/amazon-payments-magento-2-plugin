<?php

namespace Amazon\Payment\Controller\Checkout;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Domain\AmazonAddress;
use Amazon\Payment\Helper\Address;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use PayWithAmazon\ResponseInterface;

class Shipping extends Action
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var Address
     */
    protected $address;

    public function __construct(Context $context, ClientFactoryInterface $clientFactory, Address $address)
    {
        parent::__construct($context);

        $this->clientFactory = $clientFactory;
        $this->address       = $address;
    }

    public function execute()
    {
        $client = $this->clientFactory->create();

        /**
         * @var ResponseInterface $response
         */
        $response = $client->getOrderReferenceDetails(
            [
                'amazon_order_reference_id' => $this->getRequest()->getParam('amazonOrderReferenceId'),
                'address_consent_token'     => $this->getRequest()->getParam('addressConsentToken')
            ]
        );

        $amazonAddress = new AmazonAddress($response);
        $address       = $this->address->convertToMagentoEntity($amazonAddress);
        $json          = $this->address->convertToJson($address);

        $this->getResponse()->representJson($json);
    }
}