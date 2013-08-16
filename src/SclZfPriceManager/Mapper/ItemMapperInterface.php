<?php

namespace SclZfPriceManager\Mapper;

use SclZfUtilities\Mapper\GenericMapperInterface;

/**
 * Interface for mapper for {@see SclZfPriceManager\Entity\Item}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface ItemMapperInterface extends GenericMapperInterface
{
    public function findByIdentifier($identifier);
}
