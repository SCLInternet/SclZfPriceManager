<?php

namespace SclZfPriceManager\Exception;

/**
 * Thrown when problems are found searching for a price.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class PriceNotFoundException extends \LogicException implements ExceptionInterface
{
    public static function itemNotFound($identifier)
    {
        return new self("Item with identifier '$identifier' was not found.");
    }

    public static function variationNotFound($itemId, $variationId)
    {
        return new self("Variation with identifier '$itemId::$variationId' was not found.");
    }

    public static function defaultProfileNotFound($id)
    {
        return new self("The default profile was not found (ID=$id).");
    }

    public static function profileNotFound($id)
    {
        return new self("Profile with ID '$id' was not found.");
    }
}
