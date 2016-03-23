<?php

namespace Amazon\Core\Domain;

use PayWithAmazon\ResponseInterface;

class AmazonAddress
{
    /**
     * @var AmazonName
     */
    protected $name;

    /**
     * @var array
     */
    protected $lines;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $postCode;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $telephone;

    public function __construct(ResponseInterface $response)
    {
        $data = $response->toArray();
        $address
              = $data['GetOrderReferenceDetailsResult']['OrderReferenceDetails']['Destination']['PhysicalDestination'];

        $this->name = new AmazonName($address['Name']);

        $this->lines = [];

        for ($i = 0; $i <= 3; $i++) {
            $key = 'AddressLine' . $i;

            if (isset($address[$key])) {
                $this->lines[] = $address[$key];
            }
        }

        $this->city        = $address['City'];
        $this->state       = $address['StateOrRegion'];
        $this->postCode    = $address['PostalCode'];
        $this->countryCode = $address['CountryCode'];
        $this->telephone   = $address['Phone'];
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->name->getFirstName();
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->name->getLastName();
    }

    /**
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }
}