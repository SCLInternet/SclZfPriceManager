<?php

namespace SclZfPriceManager\Mapper;

use SclZfUtilities\Mapper\GenericMapperInterface;

/**
 * Interface for mapper for {@see SclZfPriceManager\Entity\PriceItem}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface PriceItemMapperInterface extends GenericMapperInterface
{
    public function findByIdentifier($identifier);
}
