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

    /**
     * AmazonCustomer constructor.
     *
     * @param string $id
     * @param string $email
     * @param string $name
     */
    public function __construct($id, $email, $name)
    {
        $this->id    = $id;
        $this->email = $email;

        $nameParts       = explode(' ', trim($name), 2);
        $this->firstName = $nameParts[0];
        $this->lastName  = isset($nameParts[1]) ? $nameParts[1] : '.';
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
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