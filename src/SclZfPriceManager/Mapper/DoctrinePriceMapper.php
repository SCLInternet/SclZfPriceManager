<?php

namespace SclZfPriceManager\Mapper;

use Doctrine\Common\Persistence\ObjectManager;
use SclZfUtilities\Mapper\GenericDoctrineMapper;
use SclZfUtilities\Doctrine\FlushLock;
use SclZfPriceManager\Entity\Profile;
use SclZfPriceManager\Entity\Variation;

/**
 * DoctrineCustomerMapper.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrinePriceMapper extends GenericDoctrineMapper implements
    PriceMapperInterface
{
    /**
     * Inject required objects.
     *
     * @param  ObjectManager $entityManager
     * @param  FlushLock     $flushLock
     */
    public function __construct(
        ObjectManager $entityManager,
        FlushLock $flushLock,
        $entityName = null
    ) {
        parent::__construct(
            $entityManager,
            $flushLock,
            'SclZfPriceManager\Entity\Price'
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param  Profile $profile
     * @param  Variation $variation
     *
     * @return \SclZfPriceManager\Entity\Price
     */
    public function findByProfileAndVariation(Profile $profile, Variation $variation)
    {
        return $this->singleEntity(
            $this->findBy(
                array(
                    'profile' => $profile,
                    'variation' => $variation,
                )
            )
        );
    }
}
