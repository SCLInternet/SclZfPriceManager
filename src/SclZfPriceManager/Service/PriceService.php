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
use SCL\Currency\TaxedPriceFactory;
use SCL\Currency\Currency;
use SCL\Currency\Money;
use SCL\Currency\MoneyFactory;

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
     * @var int
     */
    private $defaultProfile;

    /**
     * @var ProfileMapperInterface
     */
    private $profileMapper;

    /**
     * @var PriceMapperInterface
     */
    private $priceMapper;

    /**
     * @var VariationService
     */
    private $variationService;

    /**
     * @var TaxedPriceFactory
     */
    private $priceFactory;

    /**
     * @param int $defaultProfile
     */
    public function __construct(
        $defaultProfile,
        VariationService $variationService,
        ProfileMapperInterface $profileMapper,
        PriceMapperInterface $priceMapper,
        TaxedPriceFactory $priceFactory
    ) {
        $this->defaultProfile   = $defaultProfile;
        $this->variationService = $variationService;
        $this->profileMapper    = $profileMapper;
        $this->priceMapper      = $priceMapper;
        $this->priceFactory = $priceFactory;
    }

    /**
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

        return $this->priceFactory->createFromUnitsAndRate(
            $price->getAmount(),
            $price->getTaxRate()->getRate()
        );
    }

    public function savePrice(
        $itemIdentifier,
        Money $amount,
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
        $price->setAmount($amount->getUnits());

        $this->priceMapper->save($price);

        return $price;
    }

    /**
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
     * @param Variation $variation
     *
     * @return Price|null
     */
    private function getDefaultPrice(Variation $variation)
    {
        $profile = $this->getDefaultProfile();

        return $this->priceMapper->findByProfileAndVariation($profile, $variation);
    }

    /**
     * Returns the price entity for the given item and profile, will go
     * to the default if one is not found.
     *
     * @param Variation $variation
     * @param Profile   $profile
     *
     * @return Price|null
     */
    private function getActivePrice(Variation $variation, Profile $profile)
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
     * @param mixed $profileId
     *
     * @return Price|null
     *
     * @throws PriceNotFoundException If requested profile was not found.
     */
    private function loadProfile($profileId)
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
     * @param Variation $variation
     * @param Profile   $profile
     *
     * @return Price|null
     *
     * @throws PriceNotFoundException If requested profile was not found.
     */
    private function loadPriceForProfile(Variation $variation, Profile $profile)
    {
        return $this->priceMapper->findByProfileAndVariation($profile, $variation);
    }
}
