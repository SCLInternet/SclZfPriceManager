<?php

namespace SclZfPriceManager\Service;

use SclZfPriceManager\Entity\Profile;

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
     * @param  string                   $identifier
     * @param  Profile                  $profileId
     * @return \SclZfPriceManager\Price
     */
    public function getPrice($identifier, Profile $profile = null);

    /**
     * Saves a price to the database by updating or creating entities as required.
     *
     * @param  string                          $identifier
     * @param  float                           $amount
     * @param  string                          $description
     * @param  Profile                         $profileId
     * @return \SclZfPriceManager\Entity\Price
     */
    public function savePrice(
        $identifier,
        $amount,
        $description = '',
        Profile $profile = null
    );

    /**
     * Return the default price profile.
     *
     * @return Profile
     */
    public function getDefaultProfile();
}
