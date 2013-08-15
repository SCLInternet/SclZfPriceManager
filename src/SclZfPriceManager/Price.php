<?php

namespace SclZfPriceManager;

/**
 * Class for holding a taxed price.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Price
{
    /**
     * The amount
     *
     * @var mixed
     */
    protected $amount;

    /**
     * The tax rate as a percent.
     *
     * @var float
     */
    protected $taxRate;

    /**
     * The currency precision.
     *
     * @var int
     */
    protected $precision;

    /**
     * __construct
     *
     * @param  int $precision
     */
    public function __construct($precision = 2)
    {
        $this->precision = pow(10, $precision);
    }

    /**
     * Removes the decimal places from an amount.
     *
     * @param  float $amount
     * @return int
     */
    protected function removePrecision($amount)
    {
        return round($amount * $this->precision);
    }

    /**
     * Adds the decimal places back to an amount.
     *
     * @param  int $amount
     * @return float
     */
    protected function addPrecision($amount)
    {
        return $amount / $this->precision;
    }

    /**
     * Set the amount including tax.
     *
     * When this is called the tax amount will be updated depending on the
     * current tax rate.
     *
     * @param  float $amount
     * @param  float $taxRate As a percentage.
     * @return void
     */
    public function setAmountIncTax($amount, $taxRate = null)
    {
        $amount = $this->removePrecision($amount);

        if (null !== $taxRate) {
            $this->setTaxRate($taxRate);
        }

        $percent = $amount / (100 + $this->taxRate);

        $this->amount = $percent * 100;
    }

    /**
     * Set the amount excluding tax.
     *
     * When this is called the tax amount will be updated depending on the
     * current tax rate.
     *
     * @param  float $amount
     * @param  float $taxRate As a percentage.
     * @return void
     */
    public function setAmountExTax($amount, $taxRate = null)
    {
        $amount = $this->removePrecision($amount);

        if (null !== $taxRate) {
            $this->setTaxRate($taxRate);
        }

        $this->amount = $amount;
    }

    /**
     * Get the amount including the tax.
     *
     * @return float
     */
    public function getAmountIncTax()
    {
        return $this->addPrecision($this->amount * ($this->taxRate / 100 + 1));
    }

    /**
     * Get the amount excluding the tax.
     *
     * @return float
     */
    public function getAmountExTax()
    {
        return $this->addPrecision($this->amount);
    }

    /**
     * Set the tax rate.
     *
     * @param  float $rate As a percentage.
     * @return void
     */
    public function setTaxRate($rate)
    {
        $this->taxRate = $rate;
    }

    /**
     * Get the tax rate as a percentage.
     *
     * @return float
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set the tax amount.
     *
     * When this is called the rate will be recalculated from the ratio of the
     * amount and the tax amount.
     *
     * @param  mixed $taxAmount
     * @return void
     */
    public function setTax($taxAmount)
    {
        $taxAmount = $this->removePrecision($taxAmount);

        $this->taxRate = $taxAmount / ($this->amount / 100);
    }

    /**
     * Return the tax amount.
     *
     * @return float
     */
    public function getTax()
    {
        return $this->getAmountIncTax() - $this->getAmountExTax();
    }
}
