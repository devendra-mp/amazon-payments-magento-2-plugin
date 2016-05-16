<?php

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
use Magento\Framework\AppInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use PayWithAmazon\ResponseInterface;

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
     * @param Session                              $session
     * @param ClientFactoryInterface               $clientFactory
     * @param PaymentHelper                        $paymentHelper
     * @param CoreHelper                           $coreHelper
     * @param AmazonSetOrderDetailsResponseFactory $amazonSetOrderDetailsResponseFactory
     * @param QuoteLinkInterfaceFactory            $quoteLinkFactory
     */
    public function __construct(
        Session $session,
        ClientFactoryInterface $clientFactory,
        PaymentHelper $paymentHelper,
        CoreHelper $coreHelper,
        AmazonSetOrderDetailsResponseFactory $amazonSetOrderDetailsResponseFactory,
        QuoteLinkInterfaceFactory $quoteLinkFactory
    ) {
        $this->session                              = $session;
        $this->clientFactory                        = $clientFactory;
        $this->paymentHelper                        = $paymentHelper;
        $this->coreHelper                           = $coreHelper;
        $this->amazonSetOrderDetailsResponseFactory = $amazonSetOrderDetailsResponseFactory;
        $this->quoteLinkFactory                     = $quoteLinkFactory;
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
                    'Magento Version : ' . AppInterface::VERSION . ' ' .
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
            if ( ! in_array($constraint->getId(), $allowedConstraints)) {
                throw new ValidatorException(__($constraint->getErrorMessage()));
            }
        }
    }

    protected function setReservedOrderId(Quote $quote)
    {
        if ( ! $quote->getReservedOrderId()) {
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
