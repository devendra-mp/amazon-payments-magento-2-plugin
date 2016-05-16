<?php

namespace Amazon\Payment\Domain;

class AmazonConstraint
{
    const PAYMENT_METHOD_NOT_ALLOWED_ID = 'PaymentMethodNotAllowed';
    const PAYMENT_PLAN_NOT_SET_ID = 'PaymentPlanNotSet';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * AmazonConstraint constructor.
     *
     * @param string $id
     * @param string $description
     */
    public function __construct($id, $description)
    {
        $this->id          = $id;
        $this->description = $description;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getErrorMessage()
    {
        switch ($this->getId()) {
            case static::PAYMENT_METHOD_NOT_ALLOWED_ID:
            case static::PAYMENT_PLAN_NOT_SET_ID:
                return 'Please select a payment method.';
            default:
                return 'Amazon could not process your request.';
        }
    }
}