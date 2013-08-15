<?php

namespace SclZfPriceManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a single priceable item.
 *
 * @ORM\Entity
 * @ORM\Table(name="price_item")
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class PriceItem
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
     * A unique string used to find this price object.
     *
     * @var string
     */
    protected $identifier;

    /**
     * A more readable description for what this price is for.
     *
     * @var mixed
     */
    protected $description;

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
     * Sets the value of identifier
     *
     * @param  string $identifier
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = (string) $identifier;
        return $this;
    }

    /**
     * Gets the value of identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the value of description
     *
     * @param  string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
        return $this;
    }

    /**
     * Gets the value of description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
