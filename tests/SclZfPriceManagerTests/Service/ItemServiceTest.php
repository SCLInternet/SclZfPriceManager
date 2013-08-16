<?php

namespace SclZfPriceManagerTests\Service;

use SclZfPriceManager\Service\ItemService;
use SclZfPriceManager\Entity\Item;

/**
 * Unit tests for {@see ItemService}.
 *
 * @covers SclZfPriceManager\Service\ItemService
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ItemServiceTest extends \PHPUnit_Framework_TestCase
{
    const TEST_IDENTIFIER = 'the-identifier';

    protected $service;

    protected $itemMapper;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->itemMapper = $this->getMock('SclZfPriceManager\Mapper\ItemMapperInterface');

        $this->service = new ItemService($this->itemMapper);
    }

    public function testGetItemReturnsItemEntity()
    {
        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Item',
            $this->service->getItem(self::TEST_IDENTIFIER)
        );
    }

    public function testGetItemReturnsLoadedItem()
    {
        $item = $this->getMock('SclZfPriceManager\Entity\Item');

        $this->setItemToBeLoaded($item, self::TEST_IDENTIFIER);

        $this->assertSame(
            $item,
            $this->service->getItem(self::TEST_IDENTIFIER)
        );
    }

    public function testGetItemCreatesNewItemIfLoadFailed()
    {
        $this->setItemToBeLoaded(null, self::TEST_IDENTIFIER);

        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Item',
            $this->service->getItem(self::TEST_IDENTIFIER)
        );
    }

    public function testGetItemSetsValuesOfNewItem()
    {
        $description = 'The description';

        $this->setItemToBeLoaded(null, self::TEST_IDENTIFIER);

        $item = $this->service->getItem(self::TEST_IDENTIFIER, $description);

        $this->assertEquals(self::TEST_IDENTIFIER, $item->getIdentifier(), 'Identifier is incorrect.');
        $this->assertEquals($description, $item->getDescription(), 'Description is incorrect.');
    }

    public function testGetItemSavesNewlyCreatedItem()
    {
        $description = 'The description';

        $item = new Item();

        $item->setIdentifier(self::TEST_IDENTIFIER);
        $item->setDescription($description);

        $this->setItemToBeLoaded(null, self::TEST_IDENTIFIER);

        $this->itemMapper
             ->expects($this->once())
             ->method('save')
             ->with($this->equalTo($item));

        $item = $this->service->getItem(self::TEST_IDENTIFIER, $description);
    }

    public function testGetItemDoesNotCreateIfAskedToFetchOnly()
    {
        $this->setItemToBeLoaded(null, self::TEST_IDENTIFIER);

        $this->itemMapper
             ->expects($this->never())
             ->method('save');

        $this->assertFalse($this->service->getItem(self::TEST_IDENTIFIER, '', true));
    }
    /*
     * Protected methods
     */

    protected function setItemToBeLoaded($item, $expectedIdentifier)
    {
        $this->itemMapper
             ->expects($this->once())
             ->method('findByIdentifier')
             ->with($this->equalTo($expectedIdentifier))
             ->will($this->returnValue($item));
    }
}
