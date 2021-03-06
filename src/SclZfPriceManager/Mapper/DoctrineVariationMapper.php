<?php

namespace SclZfPriceManager\Mapper;

use Doctrine\Common\Persistence\ObjectManager;
use SclZfPriceManager\Entity\Item;
use SclZfGenericMapper\Doctrine\FlushLock;
use SclZfGenericMapper\DoctrineMapper as GenericDoctrineMapper;

/**
 * DoctrineCustomerMapper.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class DoctrineVariationMapper extends GenericDoctrineMapper implements
    VariationMapperInterface
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
            new \SclZfPriceManager\Entity\Variation(),
            $entityManager,
            $flushLock
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param  Item   $item
     * @return Variation[]
     */
    public function findForItem(Item $item)
    {
        return $this->findBy(array('item' => $item));
    }

    /**
     * {@inheritDoc}
     *
     * @param  Item   $item
     * @param  string $identifier
     * @return Variation
     */
    public function findForItemByIdentifier(Item $item, $identifier)
    {
        $result = $this->findBy(
            array(
                'item'       => $item,
                'identifier' => $identifier,
            )
        );

        return $this->singleEntity($result);
    }
}
