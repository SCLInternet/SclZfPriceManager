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
            new \SclZfPriceManager\Entity\Item(),
            $entityManager,
            $flushLock
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
