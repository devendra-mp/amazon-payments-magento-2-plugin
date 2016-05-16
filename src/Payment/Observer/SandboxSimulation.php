<?php

namespace Amazon\Payment\Observer;

use Amazon\Core\Helper\Data;
use Amazon\Payment\Api\Data\QuoteLinkInterfaceFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SandboxSimulation implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $coreHelper;

    /**
     * @var QuoteLinkInterfaceFactory
     */
    protected $quoteLinkFactory;

    /**
     * @param Data $coreHelper
     * @param QuoteLinkInterfaceFactory $quoteLinkFactory
     */
    public function __construct(
        Data $coreHelper,
        QuoteLinkInterfaceFactory $quoteLinkFactory
    ) {
        $this->coreHelper = $coreHelper;
        $this->quoteLinkFactory = $quoteLinkFactory;
    }

    public function execute(Observer $observer)
    {
        if ($this->coreHelper->isSandboxEnabled()) {
            $context = $observer->getEvent()->getContext();
            $payment = $observer->getEvent()->getPayment();

            $simulationReference = $this->getSimulationReference($payment);

            if ( ! empty($simulationReference)) {
                $simulationString = $this->getSimulationString($simulationReference, $context);
                if ( ! empty($simulationString)) {
                    $requestParameter = $this->getRequestParameter($context);
                    $observer->getTransport()->addData([$requestParameter => $simulationString]);
                }
            }
        }
    }

    /**
     * @param $payment
     * @return string
     */
    protected function getSimulationReference($payment) {
        $simulationReference = $this->getSimulationReferenceFromPayment($payment);
        $quoteLink = $this->getQuoteLink($payment);

        if ($simulationReference) {
            $quoteLink->setSandboxSimulationReference($simulationReference)->save();
        } else {
            $simulationReference = $quoteLink->getSandboxSimulationReference();
        }

        return $simulationReference;
    }

    /**
     * @param $payment
     * @return string
     */
    protected function getSimulationReferenceFromPayment($payment) {
        $simulationReference = null;

        $additionalInformation = $payment->getAdditionalInformation();
        if (is_array($additionalInformation) and array_key_exists('sandbox_simulation_reference', $additionalInformation)) {
            $simulationReference = $additionalInformation['sandbox_simulation_reference'];
        }

        return $simulationReference;
    }

    /**
     * @param $payment
     * @return Amazon\Payment\Api\Data\QuoteLinkInterface
     */
    protected function getQuoteLink($payment) {
        $quoteId = $payment->getOrder()->getQuoteId();
        $quoteLink = $this->quoteLinkFactory->create();
        $quoteLink->load($quoteId, 'quote_id');

        return $quoteLink;
    }

    /**
     * @return array
     */
    protected function getRequestParameters() {
        $requestParameters = [
            'authorization' => 'seller_authorization_note',
            'authorization_capture' => 'seller_authorization_note',
            'capture' => 'seller_capture_note',
        ];

        return $requestParameters;
    }

    /**
     * @param string $context
     * @return string
     */
    protected function getRequestParameter($context) {
        $requestParameter = null;

        $requestParameters = $this->getRequestParameters();
        if (array_key_exists($context, $requestParameters)) {
            $requestParameter = $requestParameters[$context];
        }

        return $requestParameter;
    }

    /**
     * @param string $simulationReference
     * @return string
     */
    protected function getSimulationString($simulationReference, $context = null) {
        $simulationString = null;

        $simulationStrings = $this->coreHelper->getSandboxSimulationStrings($context);
        if (array_key_exists($simulationReference, $simulationStrings)) {
            $simulationString = $simulationStrings[$simulationReference];
        }

        return $simulationString;
    }
}
