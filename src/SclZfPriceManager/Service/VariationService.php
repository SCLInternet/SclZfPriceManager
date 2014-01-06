<?php

namespace SclZfPriceManager\Service;

use SclZfPriceManager\Entity\Item;
use SclZfPriceManager\Entity\Variation;
use SclZfPriceManager\Exception\InvalidArgumentException;
use SclZfPriceManager\Service\ItemService;
use SclZfPriceManager\Mapper\VariationMapperInterface;

/**
 * Service for doing things with {@see Varation} entities.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class VariationService
{
    /**
     * @var ItemService
     */
    private $itemService;

    /**
     * @var VariationMapperInterface
     */
    private $variationMapper;

    /**
     * @param ItemService              $itemService
     * @param VariationMapperInterface $variationMapper
     */
    public function __construct(
        ItemService $itemService,
        VariationMapperInterface $variationMapper
    ) {
        $this->itemService     = $itemService;
        $this->variationMapper = $variationMapper;
    }

    /**
     * Returns the requested variation or creates one if required.
     *
     * @param Item|string $itemOrIdentifer
     * @param string      $variationIdentifier
     * @param string      $variationDescription
     * @param string      $itemDescription
     *
     * @return Variation|false
     */
    public function getVariation(
        $itemOrIdentifer,
        $variationIdentifier,
        $variationDescription = '',
        $itemDescription = '',
        $fetchOnly = false
    ) {
        $item = $this->itemFromItemOrIdentifier($itemOrIdentifer, $fetchOnly);

        // @todo Maybe throw if !$item and !$fetchOnly?

        if (!$item) {
            return false;
        }

        $variation = $this->variationMapper->findForItemByIdentifier($item, $variationIdentifier);

        if (!$variation && !$fetchOnly) {
            $variation = $this->createNewVariation($item, $variationIdentifier, $variationDescription);
        }

        return $variation ?: false;
    }

    /**
     * @param string $variationIdentifier
     * @param string $variationDescription
     *
     * @return Variation
     */
    private function createNewVariation(Item $item, $variationIdentifier, $variationDescription)
    {
        $variation = new Variation();

        $variation->setItem($item);
        $variation->setIdentifier($variationIdentifier);
        $variation->setDescription($variationDescription);

        $this->variationMapper->save($variation);

        return $variation;
    }

    /**
     * Takes either an item or an item indentifier and returns the item.
     *
     * @param Item|string $itemOrIdentifer
     *
     * @return Item
     */
    private function itemFromItemOrIdentifier($itemOrIdentifer, $fetchOnly)
    {
        $this->assertIsItemOrIdentifier($itemOrIdentifer);

        if (!is_object($itemOrIdentifer)) {
            $itemOrIdentifer = $this->itemService->getItem($itemOrIdentifer, $fetchOnly);
        }

        return $itemOrIdentifer;
    }

    /**
     * @param Item|string $itemOrIdentifer
     *
     * @throws InvalidArgumentException
     */
    private function assertIsItemOrIdentifier($itemOrIdentifer)
    {
        if (is_object($itemOrIdentifer) && !$itemOrIdentifer instanceof Item) {
            throw InvalidArgumentException::badType(
                '$itemOrIdentifer',
                '\SclZfPriceManager\Entity\Item or string',
                $itemOrIdentifer
            );
        }
    }
}
