<?php

namespace SclZfPriceManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaxRate
 *
 * @ORM\Entity
 * @ORM\Table(name="price_tax_rate")
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class TaxRate
{
    /**
     * Database id.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * A decent name for the tax rate.
     *
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * The tax rate as a percent.
     *
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=3)
     */
    private $rate;

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

    /**
     * Sets the value of rate
     *
     * @param  float $rate
     * @return self
     */
    public function setRate($rate)
    {
        $this->rate = (float) $rate;
        return $this;
    }

    /**
     * Gets the value of rate
     *
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }
}
