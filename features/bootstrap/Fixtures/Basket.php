<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Magento\Quote\Api\CartRepositoryInterface;

class Basket extends BaseFixture
{
    /**
     * @var CartRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(CartRepositoryInterface::class);
    }

    public function getActiveForCustomer($customerId)
    {
        return $this->repository->getActiveForCustomer($customerId);
    }
}