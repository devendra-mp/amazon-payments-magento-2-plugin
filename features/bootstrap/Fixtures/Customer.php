<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Context\Data\FixtureContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class Customer extends BaseFixture
{
    protected $defaults = [
        CustomerInterface::FIRSTNAME => 'John',
        CustomerInterface::LASTNAME  => 'Doe',
        CustomerInterface::EMAIL     => 'customer@example.com'
    ];

    /**
     * @var CustomerRepositoryInterface
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(CustomerRepositoryInterface::class);
    }

    public function create(array $data)
    {
        $data = array_merge($this->defaults, $data);
        $customerData = $this->createMagentoObject(CustomerInterface::class, ['data' => $data]);

        $customer = $this->repository->save($customerData);

        FixtureContext::trackFixture($customer, $this->repository);

        return $customer;
    }

    public function get($email)
    {
        return $this->repository->get($email);
    }
}