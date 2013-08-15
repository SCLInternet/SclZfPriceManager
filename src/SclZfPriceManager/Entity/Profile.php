<?php

namespace SclZfPriceManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a pricing profile to allow different customer groups to have different prices.
 *
 * @ORM\Entity
 * @ORM\Table(name="price_profile")
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Profile
{
    /**
     * Datebase primary key.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The name of the profile.
     *
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * Sets the value of id
     *
     * @param  int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Gets the value of id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of name
     *
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Gets the value of name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
