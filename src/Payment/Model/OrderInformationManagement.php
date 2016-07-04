<?php
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Exception\AmazonServiceUnavailableException;
use Amazon\Core\Helper\Data as CoreHelper;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Amazon\Payment\Api\OrderInformationManagementInterface;
use Amazon\Payment\Domain\AmazonSetOrderDetailsResponse;
use Amazon\Payment\Domain\AmazonSetOrderDetailsResponseFactory;
use Amazon\Payment\Helper\Data as PaymentHelper;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use PayWithAmazon\ResponseInterface;
use Psr\Log\LoggerInterface;

class OrderInformationManagement implements OrderInformationManagementInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var CoreHelper
     */
    protected $coreHelper;

    /**
     * @var AmazonSetOrderDetailsResponseFactory
     */
    protected $amazonSetOrderDetailsResponseFactory;

    /*
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProductMetadata
     */
    protected $productMetadata;

    /**
     * @param Session                              $session
     * @param ClientFactoryInterface               $clientFactory
     * @param PaymentHelper                        $paymentHelper
     * @param CoreHelper                           $coreHelper
     * @param AmazonSetOrderDetailsResponseFactory $amazonSetOrderDetailsResponseFactory
     * @param QuoteLinkInterfaceFactory            $quoteLinkFactory
     * @param LoggerInterface                      $logger
     * @param ProductMetadata                      $productMetadata
     */
    public function __construct(
        Session $session,
        ClientFactoryInterface $clientFactory,
        PaymentHelper $paymentHelper,
        CoreHelper $coreHelper,
        AmazonSetOrderDetailsResponseFactory $amazonSetOrderDetailsResponseFactory,
        QuoteLinkInterfaceFactory $quoteLinkFactory,
        LoggerInterface $logger,
        ProductMetadata $productMetadata
    ) {
        $this->session                              = $session;
        $this->clientFactory                        = $clientFactory;
        $this->paymentHelper                        = $paymentHelper;
        $this->coreHelper                           = $coreHelper;
        $this->amazonSetOrderDetailsResponseFactory = $amazonSetOrderDetailsResponseFactory;
        $this->quoteLinkFactory                     = $quoteLinkFactory;
        $this->logger                               = $logger;
        $this->productMetadata                      = $productMetadata;
    }

    /**
     * {@inheritDoc}
     */
    public function saveOrderInformation($amazonOrderReferenceId, $allowedConstraints = [])
    {
        try {
            $quote   = $this->session->getQuote();
            $storeId = $quote->getStoreId();

            $this->validateCurrency($quote->getQuoteCurrencyCode());

            $this->setReservedOrderId($quote);

            $data = [
                'amazon_order_reference_id' => $amazonOrderReferenceId,
                'amount'                    => $quote->getGrandTotal(),
                'currency_code'             => $quote->getQuoteCurrencyCode(),
                'seller_order_id'           => $quote->getReservedOrderId(),
                'store_name'                => $quote->getStore()->getName(),
                'custom_information'        =>
                    'Magento Version : ' . $this->productMetadata->getVersion() . ' ' .
                    'Plugin Version : ' . $this->paymentHelper->getModuleVersion()
                ,
                'platform_id'               => $this->coreHelper->getMerchantId(ScopeInterface::SCOPE_STORE, $storeId)
            ];

            $responseParser = $this->clientFactory->create($storeId)->setOrderReferenceDetails($data);
            $response       = $this->amazonSetOrderDetailsResponseFactory->create([
                'response' => $responseParser
            ]);

            $this->validateConstraints($response, $allowedConstraints);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error($e);
            throw new AmazonServiceUnavailableException();
        }
    }

    protected function validateCurrency($code)
    {
        if ($this->coreHelper->getCurrencyCode() !== $code) {
            throw new LocalizedException(__('The currency selected is not supported by Amazon payments'));
        }
    }

    protected function validateConstraints(AmazonSetOrderDetailsResponse $response, $allowedConstraints)
    {
        foreach ($response->getConstraints() as $constraint) {
            if (! in_array($constraint->getId(), $allowedConstraints)) {
                throw new ValidatorException(__($constraint->getErrorMessage()));
            }
        }
    }

    protected function setReservedOrderId(Quote $quote)
    {
        if (! $quote->getReservedOrderId()) {
            $quote
                ->reserveOrderId()
                ->save();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function confirmOrderReference($amazonOrderReferenceId, $storeId = null)
    {
        try {
            $response = $this->clientFactory->create($storeId)->confirmOrderReference(
                [
                    'amazon_order_reference_id' => $amazonOrderReferenceId
                ]
            );

            $this->validateResponse($response);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error($e);
            throw new AmazonServiceUnavailableException();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeOrderReference($amazonOrderReferenceId, $storeId = null)
    {
        try {
            $response = $this->clientFactory->create($storeId)->closeOrderReference(
                [
                    'amazon_order_reference_id' => $amazonOrderReferenceId
                ]
            );

            $this->validateResponse($response);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error($e);
            throw new AmazonServiceUnavailableException();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function cancelOrderReference($amazonOrderReferenceId, $storeId = null)
    {
        try {
            $response = $this->clientFactory->create($storeId)->cancelOrderReference(
                [
                    'amazon_order_reference_id' => $amazonOrderReferenceId
                ]
            );

            $this->validateResponse($response);
        } catch (LocalizedException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error($e);
            throw new AmazonServiceUnavailableException();
        }
    }

    protected function validateResponse(ResponseInterface $response)
    {
        $data = $response->toArray();

        if (200 != $data['ResponseStatus']) {
            throw new AmazonServiceUnavailableException();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function removeOrderReference()
    {
        $quote = $this->session->getQuote();

        if ($quote->getId()) {
            $quoteLink = $this->quoteLinkFactory->create()->load($quote->getId(), 'quote_id');

            if ($quoteLink->getId()) {
                $quoteLink->delete();
            }
        }
    }
}
