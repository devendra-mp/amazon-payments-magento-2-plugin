<?php

namespace Fixtures;

use Bex\Behat\Magento2InitExtension\Fixtures\BaseFixture;
use Context\Data\FixtureContext;
use Magento\Framework\Math\Random;
use Magento\User\Model\UserFactory;

class AdminUser extends BaseFixture
{
    /**
     * @var UserFactory
     */
    protected $factory;

    public function __construct()
    {
        parent::__construct();
        $this->factory = $this->getMagentoObject(UserFactory::class);
        $this->random  = $this->getMagentoObject(Random::class);
    }

    public function generate()
    {
        $data = [
            'firstname' => 'John',
            'lastname'  => 'Doe',
            'username'  => $this->getDefaultUsername(),
            'email'     => 'admin@example.com',
            'password'  => $this->getDefaultPassword(),
            'role_id'   => 1
        ];

        $user = $this->factory->create()->setData($data)->save();

        FixtureContext::trackFixture($user);

        return $user;
    }

    public function getDefaultUsername()
    {
        static $defaultUsername = null;

        if (null === $defaultUsername) {
            $defaultUsername = $this->random->getRandomString(20, Random::CHARS_LOWERS);
        }

        return $defaultUsername;
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
}