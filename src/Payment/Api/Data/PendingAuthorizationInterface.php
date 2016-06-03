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
namespace Amazon\Payment\Api\Data;

use Amazon\Payment\Model\ResourceModel\PendingAuthorization as PendingAuthorizationResourceModel;
use Exception;
use Magento\Sales\Api\Data\OrderInterface;

interface PendingAuthorizationInterface
{
    const ID = 'entity_id';
    const AUTHORIZATION_ID = 'authorization_id';
    const ORDER_ID = 'order_id';
    const PAYMENT_ID = 'payment_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get pending authorization id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get order id
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Set order id
     *
     * @param string $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId);

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
     * Get payment id
     *
     * @return integer
     */
    public function getPaymentId();

    /**
     * Set payment id
     *
     * @param integer $paymentId
     *
     * @return $this
     */
    public function setPaymentId($paymentId);

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
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set created at
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set order
     *
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order);

    /**
     * Save pending authorization
     *
     * @return $this
     * @throws Exception
     */
    public function save();

    /**
     * Delete pending authorization
     *
     * @return $this
     * @throws Exception
     */
    public function delete();

    /**
     * Load pending authorization data
     *
     * @param integer     $modelId
     * @param null|string $field
     *
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
     * @return PendingAuthorizationResourceModel
     */
    public function getResource();
}