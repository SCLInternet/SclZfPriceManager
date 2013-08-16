<?php

namespace SclZfPriceManager\Exception;

/**
 * InvalidArgumentException
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class InvalidArgumentException extends \InvalidArgumentException implements
    ExceptionInterface
{
    public static function badType($name, $expected, $got)
    {
        return new self(
            sprintf(
                '%s was expected to be instance of %s; got "%s".',
                $name,
                $expected,
                is_object($got) ? get_class($got) : gettype($got)
            )
        );
    }
}
