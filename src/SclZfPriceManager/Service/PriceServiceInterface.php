<?php

namespace SclZfPriceManager\Service;

use SclZfPriceManager\Entity\Profile;
use SclZfPriceManager\Entity\TaxRate;

/**
 * Interface for services which generate prices.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
interface PriceServiceInterface
{
    /**
     * Gets the price for the requested identifier.
     *
     * @param  string                 $itemIdentifier
     * @param  string                 $variationIdentifer
     * @param  Profile                $profile
     * @return \SclZfPriceManager\Price
     */
    public function getPrice(
        $itemIdentifer,
        $variationIdentifer = null,
        Profile $profile = null
    );

    /**
     * Saves a price to the database by updating or creating entities as required.
     *
     * @param  string                          $itemIdentifier
     * @param  float                           $amount
     * @param  TaxRate                         $taxRate
     * @param  string                          $variationIdentifier
     * @param  string                          $itemDescription
     * @param  string                          $variationDescription
     * @param  Profile                         $profile
     * @return \SclZfPriceManager\Entity\Price
     */
    public function savePrice(
        $itemIdentifier,
        $amount,
        TaxRate $taxRate,
        $variationIdentifer = null,
        $itemDescription = '',
        $variationDescription = null,
        Profile $profile = null
    );

    /**
     * Return the default price profile.
     *
     * @return Profile
     */
    public function getDefaultProfile();
}
