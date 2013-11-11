<?php

namespace SclZfPriceManager\Mapper;

use Doctrine\Common\Persistence\ObjectManager;
use SclZfGenericMapper\DoctrineMapper as GenericDoctrineMapper;
use SclZfGenericMapper\Doctrine\FlushLock;

/**
 * DoctrineCustomerMapper.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrineProfileMapper extends GenericDoctrineMapper implements
    ProfileMapperInterface
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
            new \SclZfPriceManager\Entity\Profile(),
            $entityManager,
            $flushLock
        );
    }
}
