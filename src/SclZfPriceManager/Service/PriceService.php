<?php

namespace SclZfPriceManager\Service;

use SclZfPriceManager\Entity\Price as PriceEntity;
use SclZfPriceManager\Entity\Profile;
use SclZfPriceManager\Entity\Variation;
use SclZfPriceManager\Exception\PriceNotFoundException;
use SclZfPriceManager\Mapper\PriceMapperInterface;
use SclZfPriceManager\Mapper\ProfileMapperInterface;
use SclZfPriceManager\Price;
use SclZfPriceManager\Service\VariationService;
use SclZfPriceManager\Entity\TaxRate;

/**
 * Service to load and manipulate prices.
 *
 * @covers SclZfPriceManager\Service\PriceService
 *
 * @author Tom Oram <tom@scl.co.uk>
 * @todo   Tidy up this test case as it has got a bit messy after changes in functionality in PriceService.
 */
class PriceService implements PriceServiceInterface
{
    /**
     * The ID of the default price profile.
     *
     * @var int
     */
    protected $defaultProfile;

    /**
     * Price profile mapper.
     *
     * @var ProfileMapperInterface
     */
    protected $profileMapper;

    /**
     * Price object mapper.
     *
     * @var PriceMapperInterface
     */
    protected $priceMapper;

    /**
     * The variation service.
     *
     * @var VariationService
     */
    protected $variationService;

    /**
     * __construct
     *
     * @param  int                      $defaultProfile
     * @param  VariationService         $variationService,
     * @param  ProfileMapperInterface   $profileMapper
     * @param  PriceMapperInterface     $priceMapper
     */
    public function __construct(
        $defaultProfile,
        VariationService $variationService,
        ProfileMapperInterface $profileMapper,
        PriceMapperInterface $priceMapper
    ) {
        $this->defaultProfile = $defaultProfile;
        $this->variationService = $variationService;
        $this->profileMapper  = $profileMapper;
        $this->priceMapper    = $priceMapper;
    }

    /**
     * {@inheritDoc}
     *
     * @param  string                 $itemIdentifier
     * @param  string                 $variationIdentifier
     * @param  Profile                $profile
     * @return Price
     * @throws PriceNotFoundException If the variation was not found.
     */
    public function getPrice(
        $itemIdentifier,
        $variationIdentifier = null,
        Profile $profile = null
    ) {
        $variation = $this->variationService->getVariation(
            $itemIdentifier,
            $variationIdentifier,
            '',
            '',
            true
        );

        if (!$variation) {
            throw PriceNotFoundException::variationNotFound(
                $itemIdentifier,
                $variationIdentifier
            );
        }

        $price = (null === $profile)
            ? $this->getDefaultPrice($variation)
            : $this->getActivePrice($variation, $profile);

        if (!$price) {
            return null;
        }

        return $this->createPrice(
            $price->getAmount(),
            $price->getTaxRate()->getRate()
        );
    }

    /**
     * {@inheritDoc}
     *
     * The item description is only set if the item is newly created otherwise
     * it is not changed.
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
        $variationIdentifier = null,
        $itemDescription = '',
        $variationDescription = null,
        Profile $profile = null
    ) {
        $profile = $profile ?: $this->getDefaultProfile();

        $variation = $this->variationService->getVariation(
            $itemIdentifier,
            $variationIdentifier,
            $variationDescription,
            $itemDescription
        );

        $price = $this->priceMapper->findByProfileAndVariation($profile, $variation);

        if (!$price) {
            $price = new PriceEntity();
        }

        $price->setVariation($variation);
        $price->setProfile($profile);
        $price->setTaxRate($taxRate);
        $price->setAmount($amount);

        $this->priceMapper->save($price);

        return $price;
    }

    /**
     * {@inheritDoc}
     *
     * @return Profile
     */
    public function getDefaultProfile()
    {
        try {
            return $this->loadProfile($this->defaultProfile);
        } catch (PriceNotFoundException $e) {
            throw PriceNotFoundException::defaultProfileNotFound($this->defaultProfile);
        }
    }

    /**
     * Load the default price for the given item.
     *
     * @param  Variation $variation
     * @return Price|null
     */
    protected function getDefaultPrice(Variation $variation)
    {
        $profile = $this->getDefaultProfile();

        return $this->priceMapper->findByProfileAndVariation($profile, $variation);
    }

    /**
     * Returns the price entity for the given item and profile, will go
     * to the default if one is not found.
     *
     * @param  Variation $variation
     * @param  Profile   $profile
     * @return Price|null
     */
    protected function getActivePrice(Variation $variation, Profile $profile)
    {
        $price = $this->loadPriceForProfile($variation, $profile);

        if (!$price) {
            $price = $this->getDefaultPrice($variation);
        }

        return $price;
    }

    /**
     * Returns the price entity for the given item and profile.
     *
     * @param  mixed                  $profileId
     * @return Price|null
     * @throws PriceNotFoundException If requested profile was not found.
     */
    protected function loadProfile($profileId)
    {
        $profile = $this->profileMapper->findById($profileId);

        if (!$profile) {
            throw PriceNotFoundException::profileNotFound($profileId);
        }

        return $profile;
    }

    /**
     * Returns the price entity for the given item and profile.
     *
     * @param  Variation              $variation
     * @param  Profile                $profile
     * @return Price|null
     * @throws PriceNotFoundException If requested profile was not found.
     */
    protected function loadPriceForProfile(Variation $variation, Profile $profile)
    {
        return $this->priceMapper->findByProfileAndVariation($profile, $variation);
    }

    /**
     * Creates a {@see Price} object and sets the values.
     *
     * @param  float $amount
     * @param  float $taxRate
     * @return Price
     */
    protected function createPrice($amount, $taxRate)
    {
        $price = new Price();

        $price->setAmountExTax($amount);
        $price->setTaxRate($taxRate);

        return $price;
    }
}
