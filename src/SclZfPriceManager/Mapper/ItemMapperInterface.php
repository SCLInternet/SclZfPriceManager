<?php

namespace SclZfPriceManager\Mapper;

use SclZfGenericMapper\MapperInterface as GenericMapperInterface;

/**
 * Interface for mapper for {@see SclZfPriceManager\Entity\Item}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface ItemMapperInterface extends GenericMapperInterface
{
    public function findByIdentifier($identifier);
}
