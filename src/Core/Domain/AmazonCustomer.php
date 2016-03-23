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
     * @var AmazonName
     */
    protected $name;

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
        $this->name = new AmazonName($name);
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
}