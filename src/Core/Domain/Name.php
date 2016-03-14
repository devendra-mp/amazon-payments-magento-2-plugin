<?php

namespace Amazon\Core\Domain;

class Name
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    public function __construct($name)
    {
        $parts           = explode(' ', trim($name), 2);
        $this->firstName = $parts[0];
        $this->lastName  = isset($parts[1]) ? $parts[1] : '.';
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
}