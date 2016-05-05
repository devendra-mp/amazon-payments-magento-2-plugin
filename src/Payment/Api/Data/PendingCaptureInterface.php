<?php

namespace Amazon\Payment\Api\Data;

use Amazon\Payment\Model\ResourceModel\PendingCapture as PendingCaptureResourceModel;
use Exception;

interface PendingCaptureInterface
{
    const ID = 'entity_id';
    const AUTHORIZATION_ID = 'authorization_id';
    const CREATED_AT = 'created_at';

    /**
     * Get authorization id
     *
     * @return string
     */
    public function getAuthorizationId();

    /**
     * Set authorization id
     *
     * @param string $authorizationId
     *
     * @return $this
     */
    public function setAuthorizationId($authorizationId);

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