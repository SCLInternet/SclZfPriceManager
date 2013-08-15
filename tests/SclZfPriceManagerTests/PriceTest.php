<?php

namespace SclZfPriceManagerTests;

use SclZfPriceManager\Price;

/**
 * Unit tests for {@see Price}.
 *
 * @covers SclZfPriceManager\Price
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class PriceTest extends \PHPUnit_Framework_TestCase
{
    const PRECISION = 2;

    /**
     * The object being tested.
     *
     * @var Price
     */
    protected $price;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->price = new Price(self::PRECISION);
    }

    /*
     * Basic getting and setting
     */

    public function testSettingAndGettingAnExTaxAmount()
    {
        $amount = 47.99;

        $this->price->setAmountExTax($amount);

        $this->assertEquals($amount, $this->price->getAmountExTax($amount));
    }

    public function testSetTaxRate()
    {
        $rate = 20;

        $this->price->setTaxRate($rate);

        $this->assertEquals($rate, $this->price->getTaxRate());
    }

    public function testSettingAndGettingTax()
    {
        $tax = 40;

        $this->price->setAmountExTax(100);

        $this->price->setTax($tax);

        $this->assertEquals($tax, $this->price->getTax());
    }

    /**
     * testCheckTaxIsRemovedFormSetAmountIncTax
     *
     * @depends testSettingAndGettingAnExTaxAmount
     * @depends testSetTaxRate
     *
     * @return void
     */
    public function testCheckTaxIsRemovedFormSetAmountIncTax()
    {
        $amount = 120;

        $this->price->setTaxRate(20);

        $this->price->setAmountIncTax($amount);

        $this->assertEquals(100, $this->price->getAmountExTax());
    }

    public function testSetAmountIncTaxWithTaxSetsTaxParam()
    {
        $rate = 35;

        $this->price->setAmountIncTax(100, $rate);

        $this->assertEquals($rate, $this->price->getTaxRate());
    }

    public function testCheckGetAmountIncTaxReturnsAmountIncludingTax()
    {
        $amount = 100;

        $this->price->setAmountIncTax($amount, 20);

        $this->assertEquals($amount, $this->price->getAmountIncTax());
    }

    public function testSetAmountExTaxWithTaxSetsTaxParam()
    {
        $rate = 22;

        $this->price->setAmountExTax(40, $rate);

        $this->assertEquals($rate, $this->price->getTaxRate());
    }

    public function testSetAmountExTaxAddTax()
    {
        $amount = 100;
        $rate   = 20;

        $this->price->setAmountExTax($amount, $rate);

        $this->assertEquals(120, $this->price->getAmountIncTax());
    }

    /*
     * Test tax amount gets calcuated.
     */

    public function testTaxIsCalculatedFromSetAmountExTax()
    {
        $amount = 100;
        $rate = 20;

        $this->price->setAmountExTax($amount, $rate);

        $this->assertEquals(20, $this->price->getTax());
    }

    public function testTaxIsCalculatedFromSetAmountIncTax()
    {
        $amount = 115;
        $rate = 15;

        $this->price->setAmountIncTax($amount, $rate);

        $this->assertEquals(15, $this->price->getTax());
    }

    public function testAmountIncTaxIsUpdatedWhenNewTaxValueIsSet()
    {
        $amount = 100;
        $rate = 20;

        $this->price->setAmountExTax($amount, $rate);

        $this->price->setTax(44);

        $this->assertEquals(144, $this->price->getAmountIncTax());
    }

    /*
     * Changing updating tax rates.
     */

    public function testChangingTaxRateUpdatesAmountIncTax()
    {
        $this->price->setAmountIncTax(140, 40);

        $this->price->setTaxRate(20);

        $this->assertEquals(120, $this->price->getAmountIncTax());
    }

    public function testCheckSettingTaxRateAfterAmountUpdatesTaxAmount()
    {
        $this->price->setAmountIncTax(120, 20);

        $this->price->setTaxRate(40);

        $this->assertEquals(40, $this->price->getTax());
    }
}
