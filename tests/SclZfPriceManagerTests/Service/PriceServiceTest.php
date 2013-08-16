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
        $this->itemMapper = $this->getMock('SclZfPriceManager\Mapper\PriceItemMapperInterface');

        $this->profileMapper = $this->getMock('SclZfPriceManager\Mapper\ProfileMapperInterface');

        $this->priceMapper = $this->getMock('SclZfPriceManager\Mapper\PriceMapperInterface');

        $this->service = new PriceService(self::DEFAULT_PROFILE, $this->itemMapper, $this->profileMapper, $this->priceMapper);

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

    public function testGetPriceLoadsPriceItemByIdentifier()
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
        $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $this->setExpectsToLoadDefaultProfile();

        $this->priceMapper
             ->expects($this->once())
             ->method('findForItemAndProfile')
             ->will($this->returnValue(null));

        $this->assertNull($this->service->getPrice(self::TEST_IDENTIFIER));
    }

    public function testGetPriceWithProfileRevertsToDefaultIfPriceItemNotFound()
    {
        $item = $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $profile = $this->getMock('SclZfPriceManager\Entity\Profile');
        $defaultProfile = $this->getMock('SclZfPriceManager\Entity\Profile');
        $price = $this->getMock('SclZfPriceManager\Entity\Price');

        $price->expects($this->any())
              ->method('getTaxRate')
              ->will($this->returnValue($this->taxRate));

        $this->priceMapper
             ->expects($this->at(0))
             ->method('findForItemAndProfile')
             ->with($this->identicalTo($item), $this->identicalTo($profile))
             ->will($this->returnValue(null));

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue($defaultProfile));

        $this->priceMapper
             ->expects($this->at(1))
             ->method('findForItemAndProfile')
             ->with($this->identicalTo($item), $this->identicalTo($defaultProfile))
             ->will($this->returnValue($price));

        $this->service->getPrice(self::TEST_IDENTIFIER, $profile);
    }

    public function testGetPriceThrowsWhenItemNotFound()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\PriceNotFoundException');

        $this->itemMapper
             ->expects($this->once())
             ->method('findByIdentifier')
             ->will($this->returnValue(null));

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function testGetPriceThrowsWhenDefaultProfileIsNotFound()
    {
        $this->setExpectedException(
            'SclZfPriceManager\Exception\PriceNotFoundException',
            'The default profile was not found'
        );

        $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

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

        $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Price',
            $this->service->savePrice(self::TEST_IDENTIFIER, 21)
        );
    }

    public function testSavePriceLoadsDefaultProfileWhenNoProfileIsGiven()
    {
        $this->setExpectsToLoadDefaultProfile();

        $this->service->savePrice(self::TEST_IDENTIFIER, 42);
    }

    public function testSavePriceThrowsWhenDefaultProfileCannotBeFound()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\PriceNotFoundException');

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue(null));

        $this->service->savePrice(self::TEST_IDENTIFIER, 42);
    }

    public function testSavePriceAttemptsToLoadTheItem()
    {
        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $this->service->savePrice(self::TEST_IDENTIFIER, 20);
    }

    public function testSavePriceCreatesPriceIfItemIsNotFound()
    {
        $identifier  = 'save-ident';
        $description = 'A price item';

        $saveItem = new \SclZfPriceManager\Entity\PriceItem();
        $saveItem->setIdentifier($identifier);
        $saveItem->setDescription($description);

        $this->itemMapper
             ->expects($this->once())
             ->method('findByIdentifier')
             ->will($this->returnValue(null));

        $this->itemMapper
             ->expects($this->once())
             ->method('save')
             ->with($this->equalTo($saveItem));

        $this->setExpectsToLoadDefaultProfile();

        $this->service->savePrice($identifier, 55, $description);
    }

    public function testSavePriceEntityContainsCorrectValues()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $item = $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $price = $this->service->savePrice(self::TEST_IDENTIFIER, 105);

        $this->assertSame($profile, $price->getProfile(), 'Profile is incorrect.');
        $this->assertSame($item, $price->getItem(), 'Item is incorrect');
    }

    public function testSavePriceEntityUsesGivenProfile()
    {
        $profile = $this->getMock('SclZfPriceManager\Entity\Profile');

        $item = $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $price = $this->service->savePrice(self::TEST_IDENTIFIER, 105, '', $profile);

        $this->assertSame($profile, $price->getProfile(), 'Profile is incorrect.');
    }

    public function testSavePriceSavesEntity()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $item = $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $priceToSave = new \SclZfPriceManager\Entity\Price();
        $priceToSave->setItem($item);
        $priceToSave->setProfile($profile);

        $this->priceMapper
             ->expects($this->once())
             ->method('save')
             ->with($this->equalTo($priceToSave));

        $price = $this->service->savePrice(self::TEST_IDENTIFIER, 105);
    }

    /*
     *
     * Protected methods
     *
     */

    protected function setLoadingExpectations()
    {
        $item = $this->setExpectsToLoadItem(self::TEST_IDENTIFIER);

        $profile = $this->setExpectsToLoadDefaultProfile();

        return $this->setExpectsToLoadPrice($item, $profile);
    }

    protected function setExpectsToLoadItem($identifier)
    {
        $item = $this->getMock('SclZfPriceManager\Entity\PriceItem');

        $this->itemMapper
             ->expects($this->once())
             ->method('findByIdentifier')
             ->with($this->equalTo($identifier))
             ->will($this->returnValue($item));

        return $item;
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

    protected function setExpectsToLoadPrice($item, $profile)
    {
        $priceEntity = $this->getMock('SclZfPriceManager\Entity\Price');

        $this->priceMapper
             ->expects($this->once())
             ->method('findForItemAndProfile')
             ->with($this->identicalTo($item), $this->identicalTo($profile))
             ->will($this->returnValue($priceEntity));

        $priceEntity->expects($this->any())
                    ->method('getTaxRate')
                    ->will($this->returnValue($this->taxRate));

        return $priceEntity;
    }
}
