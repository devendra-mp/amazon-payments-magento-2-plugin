<?php

namespace Amazon\Core\Domain;

class AmazonName
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * AmazonName constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $nameParts       = explode(' ', trim($name), 2);
        $this->firstName = $nameParts[0];
        $this->lastName  = isset($nameParts[1]) ? $nameParts[1] : '.';
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Get last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}