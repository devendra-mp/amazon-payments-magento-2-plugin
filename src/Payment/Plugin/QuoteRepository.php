<?php

namespace Amazon\Payment\Plugin;

use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;

class QuoteRepository
{
    /**
     * @var CartExtensionFactory
     */
    protected $cartExtensionFactory;

    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        QuoteLinkInterfaceFactory $quoteLinkFactory
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteLinkFactory     = $quoteLinkFactory;
    }

    public function afterGet(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->setAmazonOrderReferenceIdExtensionAttribute($cart);

        return $cart;
    }

    public function afterGetForCustomer(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->setAmazonOrderReferenceIdExtensionAttribute($cart);

        return $cart;
    }

    public function afterGetActive(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->setAmazonOrderReferenceIdExtensionAttribute($cart);

        return $cart;
    }

    public function afterGetActiveForCustomer(CartRepositoryInterface $cartRepository, CartInterface $cart)
    {
        $this->setAmazonOrderReferenceIdExtensionAttribute($cart);

        return $cart;
    }

    protected function setAmazonOrderReferenceIdExtensionAttribute(CartInterface $cart)
    {
        $cartExtension = ($cart->getExtensionAttributes()) ?: $this->cartExtensionFactory->create();

        $amazonQuote = $this->quoteLinkFactory->create();
        $amazonQuote->load($cart->getId(), 'quote_id');

        if ($amazonQuote->getId()) {
            $cartExtension->setAmazonOrderReferenceId($amazonQuote->getAmazonOrderReferenceId());
        }

        $cart->setExtensionAttributes($cartExtension);
    }
}