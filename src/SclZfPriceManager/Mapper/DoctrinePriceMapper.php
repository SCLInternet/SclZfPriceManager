<?php

namespace SclZfPriceManager\Mapper;

use Doctrine\Common\Persistence\ObjectManager;
use SclZfUtilitiesTest\Mapper\GenericDoctrineMapper;
use SclZfUtilities\Doctrine\FlushLock;

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
            'SclBusiness\Entity\Price'
        );
    }
}
