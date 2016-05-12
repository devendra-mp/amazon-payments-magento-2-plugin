<?php

namespace spec\Amazon\Core\Domain;

use Amazon\Core\Domain\AmazonAddress;
use Amazon\Core\Domain\AmazonAddressDe;
use Magento\Framework\App\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmazonAddressFactorySpec extends ObjectBehavior
{
    function it_returns_a_valid_de_instance()
    {
        $addressData = [
            'Name' => 'Firstname Lastname',
            'City' => 'city',
            'PostalCode' => 'PO4 CODE',
            'CountryCode' => 'DE',
            'Phone' => '123456',
            'StateOrRegion' => 'Caledonia',
        ];
        $this->beConstructedWith(ObjectManager::getInstance(), ['de' => AmazonAddressDe::class]);

        $this->create(['address' => $addressData])->shouldReturnAnInstanceOf(AmazonAddressDe::class);
    }

    function it_returns_a_default_instance_when_no_country_handlers_are_found()
    {
        $addressData = [
            'Name' => 'Firstname Lastname',
            'City' => 'city',
            'PostalCode' => 'PO4 CODE',
            'CountryCode' => 'FR',
            'Phone' => '123456',
            'StateOrRegion' => 'Caledonia',
        ];
        $this->beConstructedWith(ObjectManager::getInstance(), ['de' => AmazonAddressDe::class]);

        $this->create(['address' => $addressData])->shouldReturnAnInstanceOf(AmazonAddress::class);
    }

    function it_throws_if_the_handler_is_not_a_valid_type()
    {
        $addressData = [
            'Name' => 'Firstname Lastname',
            'City' => 'city',
            'PostalCode' => 'PO4 CODE',
            'CountryCode' => 'DE',
            'Phone' => '123456',
            'StateOrRegion' => 'Caledonia',
        ];
        $this->beConstructedWith(ObjectManager::getInstance(), ['de' => \stdClass::class]);

        $this->shouldThrow(\Magento\Framework\Exception\LocalizedException::class)
             ->during('create', [['address' => $addressData]]);
    }
}
