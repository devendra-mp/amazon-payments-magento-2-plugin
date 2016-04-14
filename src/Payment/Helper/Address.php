<?php

namespace Amazon\Payment\Helper;

use Amazon\Core\Domain\AmazonAddress;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Directory\Model\RegionFactory;

class Address
{
    /**
     * @var AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var RegionInterfaceFactory
     */
    protected $regionDataFactory;

    public function __construct(
        AddressInterfaceFactory $addressFactory,
        RegionFactory $regionFactory,
        RegionInterfaceFactory $regionDataFactory
    ) {
        $this->addressFactory    = $addressFactory;
        $this->regionFactory     = $regionFactory;
        $this->regionDataFactory = $regionDataFactory;
    }

    /**
     * Convert Amazon Address to Magento Address
     *
     * @param AmazonAddress $amazonAddress
     *
     * @return AddressInterface
     */
    public function convertToMagentoEntity(AmazonAddress $amazonAddress)
    {
        $address = $this->addressFactory->create();
        $address->setFirstname($amazonAddress->getFirstName());
        $address->setLastname($amazonAddress->getLastName());
        $address->setCity($amazonAddress->getCity());
        $address->setStreet($amazonAddress->getLines());
        $address->setPostcode($amazonAddress->getPostCode());
        $address->setTelephone($amazonAddress->getTelephone());
        $address->setCountryId(strtoupper($amazonAddress->getCountryCode()));

        $region = $this->regionFactory->create();
        $region->loadByCode($amazonAddress->getState(), $address->getCountryId());

        $regionData = $this->regionDataFactory->create();
        $regionData->setRegionId($region->getId())
            ->setRegionCode($region->getCode())
            ->setRegion($region->getDefaultName());

        $address->setRegion($regionData);

        return $address;
    }

    /**
     * Convert Magento address to array for json encode
     *
     * @param AddressInterface $address
     *
     * @return array
     */
    public function convertToArray(AddressInterface $address)
    {
        $data = [
            AddressInterface::CITY       => $address->getCity(),
            AddressInterface::FIRSTNAME  => $address->getFirstname(),
            AddressInterface::LASTNAME   => $address->getLastname(),
            AddressInterface::COUNTRY_ID => $address->getCountryId(),
            AddressInterface::STREET     => $address->getStreet(),
            AddressInterface::POSTCODE   => $address->getPostcode(),
            AddressInterface::TELEPHONE  => $address->getTelephone(),
            AddressInterface::REGION     => $address->getRegion()->getRegion(),
            'region_code'                => $address->getRegion()->getRegionCode(),
            AddressInterface::REGION_ID  => $address->getRegion()->getRegionId()
        ];

        return $data;
    }
}