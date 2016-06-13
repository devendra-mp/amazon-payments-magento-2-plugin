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
use Amazon\Payment\Api\Data\PendingRefundInterface;
use Amazon\Payment\Domain\AmazonCaptureStatus;
use Amazon\Payment\Domain\AmazonRefundDetailsResponseFactory;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Amazon\Payment\Api\Data\PendingRefundInterfaceFactory;

class QueuedRefundUpdater
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * @var ClientFactoryInterface
     */
    protected $amazonHttpClientFactory;

    /**
     * @var AmazonRefundDetailsResponseFactory
     */
    protected $amazonRefundDetailsResponseFactory;

    /**
     * @var NotifierInterface
     */
    protected $adminNotifier;

    /**
     * @var PendingRefundInterfaceFactory
     */
    protected $pendingRefundFactory;

    /**
     * @param OrderRepositoryInterface           $orderRepository
     * @param OrderPaymentRepositoryInterface    $orderPaymentRepository
     * @param ClientFactoryInterface             $amazonHttpClientFactory
     * @param AmazonRefundDetailsResponseFactory $amazonRefundDetailsResponseFactory
     * @param NotifierInterface                  $adminNotifier
     * @param PendingRefundInterfaceFactory      $pendingRefundFactory
     */
    public function __construct(
        OrderRepositoryInterface           $orderRepository,
        OrderPaymentRepositoryInterface    $orderPaymentRepository,
        ClientFactoryInterface             $amazonHttpClientFactory,
        AmazonRefundDetailsResponseFactory $amazonRefundDetailsResponseFactory,
        NotifierInterface                  $adminNotifier,
        PendingRefundInterfaceFactory      $pendingRefundFactory
    ) {
        $this->orderRepository                    = $orderRepository;
        $this->orderPaymentRepository             = $orderPaymentRepository;
        $this->amazonHttpClientFactory            = $amazonHttpClientFactory;
        $this->amazonRefundDetailsResponseFactory = $amazonRefundDetailsResponseFactory;
        $this->adminNotifier                      = $adminNotifier;
        $this->pendingRefundFactory               = $pendingRefundFactory;
    }

    /**
     * @param int $pendingRefundId
     *
     * @return void
     */
    public function checkAndUpdateRefund($pendingRefundId)
    {
        try {
            $pendingRefund = $this->pendingRefundFactory->create();
            $pendingRefund->getResource()->beginTransaction();
            $pendingRefund->setLockOnLoad(true);
            $pendingRefund->load($pendingRefundId);

            if ($pendingRefund->getRefundId()) {
                $order = $this->orderRepository->get($pendingRefund->getOrderId());

                $rawResponse = $this->amazonHttpClientFactory->create($order->getStoreId())->getRefundDetails([
                    'amazon_refund_id' => $pendingRefund->getRefundId()
                ]);

                $response = $this->amazonRefundDetailsResponseFactory->create(['response' => $rawResponse]);
                $status   = $response->getRefundDetails()->getRefundStatus();

                switch ($status->getState()) {
                    case AmazonCaptureStatus::STATE_COMPLETED:
                        $pendingRefund->delete();
                        break;
                    case AmazonCaptureStatus::STATE_DECLINED:
                        $this->triggerAdminNotificationForDeclinedRefund($pendingRefund);
                        $pendingRefund->delete();
                        break;
                }
            }

            $pendingRefund->getResource()->commit();
        } catch (\Exception $e) {
            $pendingRefund->getResource()->rollBack();
        }
    }

    /**
     * @param PendingRefundInterface $pendingRefund
     *
     * @return void
     */
    protected function triggerAdminNotificationForDeclinedRefund(PendingRefundInterface $pendingRefund)
    {
        $this->adminNotifier->addMajor(
            'Amazon Payments has declined a refund',
            "Refund ID {$pendingRefund->getRefundId()} for Order ID {$pendingRefund->getOrderId()} " .
            " has been declined by Amazon Payments."
        );
    }
}
