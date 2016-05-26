<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Context\Data\FixtureContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;

class Customer extends BaseFixture
{
    protected $defaults
        = [
            CustomerInterface::FIRSTNAME => 'John',
            CustomerInterface::LASTNAME  => 'Doe',
            CustomerInterface::EMAIL     => 'customer@example.com'
        ];

    /**
     * @var CustomerRepositoryInterface
     */
    protected $repository;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Random
     */
    protected $random;

    public function __construct()
    {
        parent::__construct();
        $this->repository = $this->getMagentoObject(CustomerRepositoryInterface::class);
        $this->encryptor  = $this->getMagentoObject(EncryptorInterface::class);
        $this->random     = $this->getMagentoObject(Random::class);
    }

    public function create(array $data)
    {
        $data         = array_merge($this->defaults, $data);
        $password     = (isset($data['password'])) ? $data['password'] : $this->getDefaultPassword();
        $passwordHash = $this->encryptor->getHash($password, true);

        $customerData = $this->createMagentoObject(CustomerInterface::class, ['data' => $data]);
        $customer     = $this->repository->save($customerData, $passwordHash);

        FixtureContext::trackFixture($customer, $this->repository);

        return $customer;
    }

    public function get($email, $ignoreRegistry = false)
    {
        $repository = ($ignoreRegistry) ? $this->createRepository() : $this->repository;
        return $repository->get($email);
    }

    public function track($email)
    {
        try {
            $customer = $this->get($email, true);
            FixtureContext::trackFixture($customer, $this->repository);
        } catch (NoSuchEntityException $e) {
            //entity not created no need to track for deletion
        }
    }

    public function getDefaultPassword()
    {
        static $defaultPassword = null;

        if (null === $defaultPassword) {
            $defaultPassword
                = $this->random->getRandomString(7, Random::CHARS_LOWERS)
                . $this->random->getRandomString(7, Random::CHARS_UPPERS)
                . $this->random->getRandomString(6, Random::CHARS_DIGITS);
        }

        return $defaultPassword;
    }

    protected function createRepository()
    {
        return $this->createMagentoObject(CustomerRepositoryInterface::class);
    }
}