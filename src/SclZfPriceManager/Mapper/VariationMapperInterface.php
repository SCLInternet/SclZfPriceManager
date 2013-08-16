<?php

namespace SclZfPriceManager\Mapper;

use SclZfUtilities\Mapper\GenericMapperInterface;
use SclZfPriceManager\Entity\Item;

/**
 * Interface for mapper for {@see SclZfPriceManager\Entity\Variation}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface VariationMapperInterface extends GenericMapperInterface
{
    /**
     * Find the variations for a give Item.
     *
     * @param  Item   $item
     * @return Variation[]
     */
    public function findForItem(Item $item);

    /**
     * Find a variation for the given Item and identifier.
     *
     * @param  Item   $item
     * @param  string $identifier
     * @return Variation
     */
    public function findForItemByIdentifier(Item $item, $identifier);
}
