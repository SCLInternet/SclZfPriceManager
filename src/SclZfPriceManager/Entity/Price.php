<?php

namespace SclZfPriceManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * An action price for an item in given profile.
 *
 * @ORM\Entity
 * @ORM\Table(name="price")
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Price
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
     * The item this price is for.
     *
     * @var Variation
     *
     * @ORM\ManyToOne(targetEntity="Variation")
     */
    protected $variation;

    /**
     * The profile this price belongs in.
     *
     * @var Profile
     *
     * @ORM\ManyToOne(targetEntity="Profile")
     */
    protected $profile;

    /**
     * The actual amount.
     *
     * @var float
     *
     * @ORM\Column(type="decimal", precision=11, scale=2)
     */
    protected $amount;

    /**
     * The tax rate to be applied to this item.
     *
     * @var TaxRate
     *
     * @ORM\ManyToOne(targetEntity="TaxRate")
     */
    protected $taxRate;

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
     * Sets the value of variation
     *
     * @param  Variation $variation
     * @return self
     */
    public function setVariation(Variation $variation)
    {
        $this->variation = $variation;
        return $this;
    }

    /**
     * Gets the value of variation
     *
     * @return Variation
     */
    public function getVariation()
    {
        return $this->variation;
    }

    /**
     * Sets the value of profile
     *
     * @param  Profile $profile
     * @return self
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Gets the value of profile
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Sets the value of amount
     *
     * @param  float $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = (float) $amount;
        return $this;
    }

    /**
     * Gets the value of amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Sets the value of taxRate
     *
     * @param  TaxRate $taxRate
     * @return self
     */
    public function setTaxRate(TaxRate $taxRate)
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * Gets the value of taxRate
     *
     * @return TaxRate
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }
}
