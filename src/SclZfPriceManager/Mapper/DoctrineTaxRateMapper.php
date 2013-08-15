<?php

namespace SclZfPriceManager\Mapper;

use Doctrine\Common\Persistence\ObjectManager;
use SclZfUtilities\Mapper\GenericDoctrineMapper;
use SclZfUtilities\Doctrine\FlushLock;

/**
 * DoctrineCustomerMapper.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrineTaxRateMapper extends GenericDoctrineMapper implements
    TaxRateMapperInterface
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
            'SclBusiness\Entity\TaxRate'
        );
    }
}
