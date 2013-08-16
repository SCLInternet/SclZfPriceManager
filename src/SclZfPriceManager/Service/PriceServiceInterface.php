<?php

namespace SclZfPriceManager\Service;

/**
 * Interface for services which generate prices.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface PriceServiceInterface
{
    public function getPrice($identifier, $profileId = null);

    public function savePrice($identifier, $amount, $description = '', $profileId = null);

    public function getDefaultProfile();
}
