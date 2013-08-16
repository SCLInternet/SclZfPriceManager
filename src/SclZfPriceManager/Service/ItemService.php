<?php

namespace SclZfPriceManager\Service;

use SclZfPriceManager\Entity\Item;
use SclZfPriceManager\Mapper\ItemMapperInterface;

/**
 * Service for doing things with {@see Item} objects.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ItemService
{
    protected $itemMapper;

    /**
     * __construct
     *
     * @param  ItemMapperInterface $itemMapper
     */
    public function __construct(ItemMapperInterface $itemMapper)
    {
        $this->itemMapper = $itemMapper;
    }

    /**
     * Returns the item with the given identifier, if it doesn't exist it is created.
     *
     * @param  string     $identifier
     * @prarm  string     $description
     * @param  bool       $fetchOnly
     * @return Item|false
     */
    public function getItem($identifier, $description = '', $fetchOnly = false)
    {
        $item = $this->itemMapper->findByIdentifier($identifier);

        if ($item) {
            return $item;
        }

        if ($fetchOnly) {
            return false;
        }

        $item = new Item();

        $item->setIdentifier($identifier);
        $item->setDescription($description);

        $this->itemMapper->save($item);

        return $item;
    }
}
