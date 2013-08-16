<?php

namespace SclZfPriceManager\Mapper;

use SclZfUtilities\Mapper\GenericMapperInterface;
use SclZfPriceManager\Entity\Profile;
use SclZfPriceManager\Entity\Variation;

/**
 * Interface for mapper for {@see SclZfPriceManager\Entity\Price}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface PriceMapperInterface extends GenericMapperInterface
{
    public function findByProfileAndVariation(Profile $profile, Variation $variation);
}
