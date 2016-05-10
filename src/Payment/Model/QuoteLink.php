<?php

namespace Amazon\Payment\Model;

use Amazon\Payment\Api\Data\QuoteLinkInterface;
use Amazon\Payment\Model\ResourceModel\QuoteLink as QuoteLinkResourceModel;
use Magento\Framework\Model\AbstractModel;

class QuoteLink extends AbstractModel implements QuoteLinkInterface
{
    protected function _construct()
    {
        $this->_init(QuoteLinkResourceModel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setAmazonOrderReferenceId($amazonOrderReferenceId)
    {
        return $this->setData('amazon_order_reference_id', $amazonOrderReferenceId);
    }

    /**
     * {@inheritDoc}
     */
    public function getAmazonOrderReferenceId()
    {
        return $this->getData('amazon_order_reference_id');
    }

    /**
     * {@inheritDoc}
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData('quote_id', $quoteId);
    }

    /**
     * {@inheritDoc}
     */
    public function getQuoteId()
    {
        return $this->getData('quote_id');
    }

    /**
     * {@inheritDoc}
     */
    public function setSandboxSimulationReference($sandboxSimulationReference)
    {
        return $this->setData('sandbox_simulation_reference', $sandboxSimulationReference);
    }

    /**
     * {@inheritDoc}
     */
    public function getSandboxSimulationReference()
    {
        return $this->getData('sandbox_simulation_reference');
    }

    /**
     * {@inheritDoc}
     */
    public function setConfirmed($confirmed)
    {
        return $this->setData('confirmed', $confirmed);
    }

    /**
     * {@inheritDoc}
     */
    public function isConfirmed()
    {
        return (bool) $this->getData('confirmed');
    }
}
