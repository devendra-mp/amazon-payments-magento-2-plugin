<?php

namespace Amazon\Core\Domain;

class AmazonCustomer
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    public function __construct($id, $email, $name)
    {
        $this->id = $id;
        $this->email = $email;

        $nameParts           = explode(' ', trim($name), 2);
        $this->firstName = $nameParts[0];
        $this->lastName  = isset($nameParts[1]) ? $nameParts[1] : '.';
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
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