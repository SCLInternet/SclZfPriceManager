<?php

namespace SclZfPriceManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a single priceable item.
 *
 * @ORM\Entity
 * @ORM\Table(name="price_variation")
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Variation
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
    private $id;

    /**
     * The item that this is a variation of.
     *
     * @var Item
     *
     * @ORM\ManyToOne(targetEntity="Item")
     */
    private $item;

    /**
     * A unique string used to find this price object.
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $identifier;

    /**
     * A more readable description for what this price is for.
     *
     * @var mixed
     *
     * @ORM\Column(type="string")
     */
    private $description;

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
     * Sets the value of item
     *
     * @param  Item $item
     * @return self
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Gets the value of item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
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
