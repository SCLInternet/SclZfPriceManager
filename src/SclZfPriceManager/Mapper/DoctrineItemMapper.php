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
class DoctrineItemMapper extends GenericDoctrineMapper implements
    ItemMapperInterface
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
            'SclZfPriceManager\Entity\Item'
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param  string $identifier
     * @return \SclZfPriceManager\Entity\Item
     */
    public function findByIdentifier($identifier)
    {
        return $this->singleEntity(
            $this->findBy(array('identifier' => $identifier))
        );
    }
}
