<?php

namespace SclZfPriceManagerTests\Service;

use SclZfPriceManager\Service\PriceService;

/**
 * Unit tests for {@see PriceService}.
 *
 * @covers SclZfPriceManager\Service\PriceService
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class PriceServiceTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_PROFILE = 7;
    const TEST_IDENTIFIER = 'test-indentifier';

    protected $itemMapper;

    protected $itemService;

    protected $variationService;

    protected $profileMapper;

    protected $priceMapper;

    protected $taxRate;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->variationService = $this->getMockBuilder('SclZfPriceManager\Service\VariationService')
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->profileMapper = $this->getMock('SclZfPriceManager\Mapper\ProfileMapperInterface');

        $this->priceMapper = $this->getMock('SclZfPriceManager\Mapper\PriceMapperInterface');

        $this->service = new PriceService(
            self::DEFAULT_PROFILE,
            $this->variationService,
            $this->profileMapper,
            $this->priceMapper
        );

        $this->taxRate = $this->getMock('SclZfPriceManager\Entity\TaxRate');

    }

    public function testPriceServiceImplementsPriceServiceInterface()
    {
        $this->assertInstanceOf('SclZfPriceManager\Service\PriceServiceInterface', $this->service);
    }

    /*
     *
     * getPrice()
     *
     */

    public function testGetPriceReturnsPrice()
    {
        $this->setLoadingExpectations();

        $this->assertInstanceOf('SclZfPriceManager\Price', $this->service->getPrice(self::TEST_IDENTIFIER));
    }

    public function testGetPriceLoadsItemByIdentifier()
    {
        $this->setLoadingExpectations();

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function testGetPriceLoadsDefaultProfileWhenNoProfileSpecified()
    {
        $this->setLoadingExpectations();

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function testGetPriceLoadsCorrectPriceEntity()
    {
        $this->setLoadingExpectations();

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function testGetPriceReturnsPriceWithCorrectAmount()
    {
        $amount     = 50.50;
        $taxRate    = 17.5;

        $priceEntity = $this->setLoadingExpectations();


        $priceEntity->expects($this->any())
                    ->method('getAmount')
                    ->will($this->returnValue($amount));


        $this->taxRate->expects($this->any())
                      ->method('getRate')
                      ->will($this->returnValue($taxRate));

        $price = $this->service->getPrice(self::TEST_IDENTIFIER);

        $this->assertEquals($amount, $price->getAmountExTax(), 'Amount is incorrect.');
        $this->assertEquals($taxRate, $price->getTaxRate(), 'Tax rate is incorrect.');
    }

    public function testGetPriceReturnsNullIfPriceEntityIsNotFound()
    {
        $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->setExpectsToLoadDefaultProfile();

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->will($this->returnValue(null));

        $this->assertNull($this->service->getPrice(self::TEST_IDENTIFIER));
    }

    public function testGetPriceWithProfileRevertsToDefaultIfItemNotFound()
    {
        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $profile        = $this->getMock('SclZfPriceManager\Entity\Profile');
        $defaultProfile = $this->getMock('SclZfPriceManager\Entity\Profile');
        $price          = $this->getMock('SclZfPriceManager\Entity\Price');

        $price->expects($this->any())
              ->method('getTaxRate')
              ->will($this->returnValue($this->taxRate));

        $this->priceMapper
             ->expects($this->at(0))
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation))
             ->will($this->returnValue(null));

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue($defaultProfile));

        $this->priceMapper
             ->expects($this->at(1))
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($defaultProfile), $this->identicalTo($variation))
             ->will($this->returnValue($price));

        $this->service->getPrice(self::TEST_IDENTIFIER, null, $profile);
    }

    public function testGetPriceThrowsWhenVariationNotFound()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\PriceNotFoundException');

        $this->variationService
             ->expects($this->once())
             ->method('getVariation')
             ->will($this->returnValue(null));

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function testGetPriceThrowsWhenDefaultProfileIsNotFound()
    {
        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->setExpectedException(
            'SclZfPriceManager\Exception\PriceNotFoundException',
            'The default profile was not found'
        );

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue(null));

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    /*
     *
     * savePrice()
     *
     */

    public function testSavePriceReturnsPriceEntity()
    {
        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Price',
            $this->service->savePrice(self::TEST_IDENTIFIER, 21, $this->taxRate)
        );
    }

    public function testSavePriceFetchesVariation()
    {
        $itemIdentifier       = 'item-id';
        $variationIdentifier  = 'var-id';
        $itemDescription      = 'item-desc';
        $variationDescription = 'var-desc';

        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToGetVariation(
            $itemIdentifier,
            $variationIdentifier,
            $itemDescription,
            $variationDescription
        );

        $this->service->savePrice(
            $itemIdentifier,
            100,
            $this->taxRate,
            $variationIdentifier,
            $variationDescription,
            $itemDescription
        );
    }

    public function testSavePriceLoadsDefaultProfileWhenNoProfileIsGiven()
    {
        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->service->savePrice(self::TEST_IDENTIFIER, 42, $this->taxRate);
    }

    public function testSavePriceThrowsWhenDefaultProfileCannotBeFound()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\PriceNotFoundException');

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue(null));

        $this->service->savePrice(self::TEST_IDENTIFIER, 42, $this->taxRate);
    }

    public function testSavePriceLoadsPrice()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation));

        $this->service->savePrice(self::TEST_IDENTIFIER, 42, $this->taxRate);
    }

    public function testSavePriceEntityContainsCorrectValues()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $price = $this->service->savePrice(self::TEST_IDENTIFIER, 105, $this->taxRate);

        $this->assertSame($profile, $price->getProfile(), 'Profile is incorrect.');
        $this->assertSame($variation, $price->getVariation(), 'Variation is incorrect');
    }

    public function testSavePriceEntityUsesGivenProfile()
    {
        $profile = $this->getMock('SclZfPriceManager\Entity\Profile');

        $item = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $price = $this->service->savePrice(self::TEST_IDENTIFIER, 105, $this->taxRate, null, '', '', $profile);

        $this->assertSame($profile, $price->getProfile(), 'Profile is incorrect.');
    }

    public function testSavePriceSavesEntity()
    {
        $amount = 105;

        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $priceToSave = new \SclZfPriceManager\Entity\Price();
        $priceToSave->setVariation($variation);
        $priceToSave->setProfile($profile);
        $priceToSave->setTaxRate($this->taxRate);
        $priceToSave->setAmount($amount);

        $this->priceMapper
             ->expects($this->once())
             ->method('save')
             ->with($this->equalTo($priceToSave));

        $price = $this->service->savePrice(self::TEST_IDENTIFIER, $amount, $this->taxRate);
    }

    public function testSavePriceLoadsExistingPriceIfItExists()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation));

        $this->service->savePrice(self::TEST_IDENTIFIER, 201, $this->taxRate);
    }

    public function testSavePriceUpdatesExistingPriceAmount()
    {
        $amount = 201;

        $price = new \SclZfPriceManager\Entity\Price();

        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation))
             ->will($this->returnValue($price));

        $result = $this->service->savePrice(self::TEST_IDENTIFIER, $amount, $this->taxRate);

        $this->assertSame($price, $result);

        $this->assertSame($this->taxRate, $result->getTaxRate());
        $this->assertEquals($amount, $result->getAmount());
    }

    /*
     *
     * Protected methods
     *
     */


    protected function setLoadingExpectations()
    {
        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $profile = $this->setExpectsToLoadDefaultProfile();

        return $this->setExpectsToLoadPrice($variation, $profile);
    }

    protected function setExpectsToGetVariation($itemId, $varId = null, $varDesc = '', $itemDesc = '')
    {
        $variation = $this->getMock('SclZfPriceManager\Entity\Variation');

        $this->variationService
             ->expects($this->once())
             ->method('getVariation')
             ->with(
                $this->equalTo($itemId),
                $this->equalTo($varId),
                $this->equalTo($varDesc),
                $this->equalTo($itemDesc)
             )
             ->will($this->returnValue($variation));

        return $variation;
    }

    protected function setExpectsToLoadDefaultProfile()
    {
        $profile = $this->getMock('SclZfPriceManager\Entity\Profile');

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue($profile));

        return $profile;
    }

    protected function setExpectsToLoadPrice($variation, $profile)
    {
        $priceEntity = $this->getMock('SclZfPriceManager\Entity\Price');

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation))
             ->will($this->returnValue($priceEntity));

        $priceEntity->expects($this->any())
                    ->method('getTaxRate')
                    ->will($this->returnValue($this->taxRate));

        return $priceEntity;
    }
}
