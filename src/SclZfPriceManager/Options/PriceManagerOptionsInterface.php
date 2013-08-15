<?php

namespace SclZfPriceManager\Options;

/**
 * Options for the price manager module.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface PriceManagerOptionsInterface
{
    public function setDefaultProfile($profile);

    public function getDefaultProfile();
}
