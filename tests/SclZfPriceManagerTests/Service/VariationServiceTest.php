<?php

namespace SclZfPriceManagerTests\Service;

use SclZfPriceManager\Service\VariationService;
use SclZfPriceManager\Entity\Item;
use SclZfPriceManager\Entity\Variation;

/**
 * Unit tests for {@see VariationService}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class VariationServiceTest extends \PHPUnit_Framework_TestCase
{
    const TEST_ITEM_IDENTIFIER = 'item-id';
    const TEST_IDENTIFIER      = 'variation-id';

    protected $service;

    protected $itemService;

    protected $variationMapper;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->itemService = $this->getMockBuilder('SclZfPriceManager\Service\ItemService')
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->variationMapper = $this->getMock('SclZfPriceManager\Mapper\VariationMapperInterface');

        $this->service = new VariationService($this->itemService, $this->variationMapper);
    }

    public function testGetVariationReturnsVariationEntity()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $variation = $this->getMock('SclZfPriceManager\Entity\Variation');

        $this->setExpectedVariationToLoad($variation, $item, self::TEST_IDENTIFIER);

        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Variation',
            $this->service->getVariation($item, self::TEST_IDENTIFIER)
        );
    }

    public function testGetVariationFetchesTheItemIfIdentifierIsGiven()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setExpectedItemToLoad($item, self::TEST_ITEM_IDENTIFIER);

        $this->service->getVariation(self::TEST_ITEM_IDENTIFIER, self::TEST_IDENTIFIER);
    }

    public function testGetVariationFetchesTheItemFetchOnlyIfInFetchOnlyMode()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->itemService
             ->expects($this->once())
             ->method('getItem')
             ->with($this->equalTo(self::TEST_ITEM_IDENTIFIER), $this->equalTo(true))
             ->will($this->returnValue($item));

        $this->service->getVariation(self::TEST_ITEM_IDENTIFIER, self::TEST_IDENTIFIER, '', '', true);
    }

    public function testGetVariationThrowsIfItemIsNotItemObject()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\InvalidArgumentException');

        $this->service->getVariation(new \stdClass(), self::TEST_IDENTIFIER);
    }

    /**
     * @depends testGetVariationFetchesTheItemIfIdentifierIsGiven
     */
    public function testGetVariationTriesToLoadExistingVariation()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setExpectedItemToLoad($item, self::TEST_ITEM_IDENTIFIER);

        $this->variationMapper
             ->expects($this->once())
             ->method('findForItemByIdentifier')
             ->with($this->identicalTo($item), $this->equalTo(self::TEST_IDENTIFIER));

        $this->service->getVariation(self::TEST_ITEM_IDENTIFIER, self::TEST_IDENTIFIER);
    }

    public function testGetVariationUsedPassedInItemIfGiven()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->variationMapper
             ->expects($this->once())
             ->method('findForItemByIdentifier')
             ->with($this->identicalTo($item), $this->equalTo(self::TEST_IDENTIFIER));

        $this->service->getVariation($item, self::TEST_IDENTIFIER);
    }

    /**
     * @depends testGetVariationUsedPassedInItemIfGiven
     */
    public function testGetVariationReturnsLoadedVariation()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $variation = $this->getMock('SclZfPriceManager\Entity\Variation');

        $this->setExpectedVariationToLoad($variation, $item, self::TEST_IDENTIFIER);

        $this->assertSame(
            $variation,
            $this->service->getVariation($item, self::TEST_IDENTIFIER)
        );
    }

    /**
     * @depends testGetVariationUsedPassedInItemIfGiven
     */
    public function testGetVariationCreatesVariationIfOneIsNotFound()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setExpectedVariationToLoad(null, $item, self::TEST_IDENTIFIER);

        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Variation',
            $this->service->getVariation($item, self::TEST_IDENTIFIER)
        );
    }

    /**
     * @depends testGetVariationUsedPassedInItemIfGiven
     */
    public function testGetVariationDoesNotCreateIfSetToFetchOnly()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setExpectedVariationToLoad(null, $item, self::TEST_IDENTIFIER);

        $this->variationMapper
             ->expects($this->never())
             ->method('save');

        $this->assertFalse(
            $this->service->getVariation($item, self::TEST_IDENTIFIER, '', '', true)
        );
    }

    /**
     * @depends testGetVariationCreatesVariationIfOneIsNotFound
     */
    public function testGetVariationSetsValuesForNewVariation()
    {
        $description = 'The description';

        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setExpectedVariationToLoad(null, $item, self::TEST_IDENTIFIER);

        $variation = $this->service->getVariation($item, self::TEST_IDENTIFIER, $description);

        $this->assertEquals(self::TEST_IDENTIFIER, $variation->getIdentifier(), 'The identifier is incorrect.');
        $this->assertSame($item, $variation->getItem(), 'The item is incorrect.');
        $this->assertEquals($description, $variation->getDescription(), 'The description is incorrect.');
    }

    /**
     * @depends testGetVariationCreatesVariationIfOneIsNotFound
     */
    public function testGetVariationSavesNewVariation()
    {
        $description = 'The description';

        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setExpectedVariationToLoad(null, $item, self::TEST_IDENTIFIER);

        $variationToSave = new Variation();
        $variationToSave->setItem($item);
        $variationToSave->setIdentifier(self::TEST_IDENTIFIER);

        $this->variationMapper
             ->expects($this->once())
             ->method('save')
             ->with($this->equalTo($variationToSave));

        $this->service->getVariation($item, self::TEST_IDENTIFIER);
    }

    /*
     * Protected methods
     */

    protected function setExpectedVariationToLoad($variation, Item $item, $identifier)
    {
        $this->variationMapper
             ->expects($this->once())
             ->method('findForItemByIdentifier')
             ->with($this->identicalTo($item), $this->equalTo($identifier))
             ->will($this->returnValue($variation));
    }

    protected function setExpectedItemToLoad($item, $identifier)
    {
        $this->itemService
             ->expects($this->once())
             ->method('getItem')
             ->with($this->equalTo($identifier), $this->equalTo(false))
             ->will($this->returnValue($item));
    }
}
