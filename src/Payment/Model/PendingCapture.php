<?php

namespace Amazon\Payment\Model;

use Amazon\Payment\Api\Data\PendingCaptureInterface;
use Amazon\Payment\Model\ResourceModel\PendingCapture as PendingCaptureResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class PendingCapture extends AbstractModel implements PendingCaptureInterface
{
    /**
     * @var DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @var boolean
     */
    protected $lockOnLoad = false;

    /**
     * PendingCapture constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param DateTimeFactory       $dateFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTimeFactory $dateFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );

        $this->dateFactory = $dateFactory;
    }

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(PendingCaptureResourceModel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getCaptureId()
    {
        return $this->getData(PendingCaptureInterface::CAPTURE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setCaptureId($captureId)
    {
        return $this->setData(PendingCaptureInterface::CAPTURE_ID, $captureId);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(PendingCaptureInterface::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(PendingCaptureInterface::CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave()
    {
        if ( ! $this->getId()) {
            $this->setCreatedAt($this->dateFactory->create()->gmtDate());
        }

        return parent::beforeSave();
    }

    /**
     * {@inheritDoc}
     */
    public function setLockOnLoad($lockOnLoad)
    {
        $this->lockOnLoad = $lockOnLoad;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLockOnLoad()
    {
        return $this->lockOnLoad;
    }
}