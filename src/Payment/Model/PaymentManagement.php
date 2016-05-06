<?php

namespace Amazon\Payment\Model;

use Amazon\Core\Client\ClientFactoryInterface;
use Amazon\Core\Helper\Data as CoreHelper;
use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Api\Data\PendingCaptureInterfaceFactory;
use Amazon\Payment\Api\PaymentManagementInterface;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponse;
use Amazon\Payment\Domain\AmazonCaptureDetailsResponseFactory;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Exception;

class PaymentManagement implements PaymentManagementInterface
{
    /**
     * @var ClientFactoryInterface
     */
    protected $clientFactory;

    /**
     * @var PendingCaptureInterfaceFactory
     */
    protected $pendingCaptureFactory;

    /**
     * @var CoreHelper
     */
    protected $coreHelper;

    /**
     * @var AmazonCaptureDetailsResponseFactory
     */
    protected $amazonCaptureDetailsResponseFactory;

    public function __construct(
        PendingCaptureInterfaceFactory $pendingCaptureFactory,
        ClientFactoryInterface $clientFactory,
        CoreHelper $coreHelper,
        AmazonCaptureDetailsResponseFactory $amazonCaptureDetailsResponseFactory
    ) {
        $this->clientFactory                       = $clientFactory;
        $this->pendingCaptureFactory               = $pendingCaptureFactory;
        $this->coreHelper                          = $coreHelper;
        $this->amazonCaptureDetailsResponseFactory = $amazonCaptureDetailsResponseFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function updateCapture($pendingCaptureId)
    {
        try {
            $pendingCapture = $this->pendingCaptureFactory->create();
            $pendingCapture->getResource()->beginTransaction();
            $pendingCapture->setLockOnLoad(true);
            $pendingCapture->load($pendingCaptureId);

            if ($pendingCapture->getCaptureId()) {
                $responseParser = $this->clientFactory->create()->getCaptureDetails([
                    'merchant_id'       => $this->coreHelper->getMerchantId(),
                    'amazon_capture_id' => $pendingCapture->getCaptureId()
                ]);

                $response = $this->amazonCaptureDetailsResponseFactory->create(['response' => $responseParser]);
                $this->processUpdateCaptureResponse($response, $pendingCapture);
            }

            $pendingCapture->getResource()->commit();
        } catch (Exception $e) {
            $pendingCapture->getResource()->rollBack();
        }
    }

    protected function processUpdateCaptureResponse(
        AmazonCaptureDetailsResponse $response,
        PendingCaptureInterface $pendingCapture
    ) {
        $status = $response->getStatus();

        switch ($status->getState()) {
            case AmazonCaptureStatus::STATE_COMPLETED:
                $this->completePendingCapture($pendingCapture);
                break;
            case AmazonCaptureStatus::STATE_DECLINED:
                $this->declinePendingCapture($pendingCapture);
                break;
        }
    }

    protected function completePendingCapture(PendingCaptureInterface $pendingCapture)
    {
        //$pendingCapture->delete();
    }

    protected function declinePendingCapture(PendingCaptureInterface $pendingCapture)
    {
        //$pendingCapture->delete();
    }
}