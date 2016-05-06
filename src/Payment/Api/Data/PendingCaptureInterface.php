<?php

namespace Amazon\Payment\Api\Data;

use Amazon\Payment\Model\ResourceModel\PendingCapture as PendingCaptureResourceModel;
use Exception;

interface PendingCaptureInterface
{
    const ID = 'entity_id';
    const CAPTURE_ID = 'capture_id';
    const CREATED_AT = 'created_at';

    /**
     * Get pending capture id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get capture id
     *
     * @return string
     */
    public function getCaptureId();

    /**
     * Set capture id
     *
     * @param string $captureId
     *
     * @return $this
     */
    public function setCaptureId($captureId);

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
    
    /**
     * Save pending capture
     *
     * @return $this
     * @throws Exception
     */
    public function save();

    /**
     * Delete pending capture
     *
     * @return $this
     * @throws Exception
     */
    public function delete();

    /**
     * Load pending capture data
     *
     * @param integer $modelId
     * @param null|string $field
     * @return $this
     */
    public function load($modelId, $field = null);

    /**
     * Set whether to lock db record on load
     *
     * @param boolean $lockOnLoad
     *
     * @return $this
     */
    public function setLockOnLoad($lockOnLoad);

    /**
     * Get whether to lock db record on load
     *
     * @return boolean
     */
    public function getLockOnLoad();


    /**
     * Retrieve model resource
     *
     * @return PendingCaptureResourceModel
     */
    public function getResource();
}