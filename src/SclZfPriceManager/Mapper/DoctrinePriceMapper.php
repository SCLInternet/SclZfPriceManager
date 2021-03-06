<?php

namespace SclZfPriceManager\Mapper;

use Doctrine\Common\Persistence\ObjectManager;
use SclZfGenericMapper\DoctrineMapper as GenericDoctrineMapper;
use SclZfGenericMapper\Doctrine\FlushLock;
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
            new \SclZfPriceManager\Entity\Price(),
            $entityManager,
            $flushLock
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
