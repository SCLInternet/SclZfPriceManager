<?php

namespace SclZfPriceManager\Service;

use SclZfPriceManager\Entity\Price as PriceEntity;
use SclZfPriceManager\Entity\PriceItem;
use SclZfPriceManager\Entity\Profile;
use SclZfPriceManager\Exception\PriceNotFoundException;
use SclZfPriceManager\Mapper\PriceItemMapperInterface;
use SclZfPriceManager\Mapper\PriceMapperInterface;
use SclZfPriceManager\Mapper\ProfileMapperInterface;
use SclZfPriceManager\Price;

/**
 * Service to load and manipulate prices.
 *
 * @author Tom Oram <tom@scl.co.uk>
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
     * PriceItem mapper.
     *
     * @var PriceItemMapperInterface
     */
    protected $itemMapper;

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
     * __construct
     *
     * @param  int                      $defaultProfile
     * @param  PriceItemMapperInterface $itemMapper
     * @param  ProfileMapperInterface   $profileMapper
     * @param  PriceMapperInterface     $priceMapper
     */
    public function __construct(
        $defaultProfile,
        PriceItemMapperInterface $itemMapper,
        ProfileMapperInterface $profileMapper,
        PriceMapperInterface $priceMapper
    ) {
        $this->defaultProfile = $defaultProfile;
        $this->itemMapper     = $itemMapper;
        $this->profileMapper  = $profileMapper;
        $this->priceMapper    = $priceMapper;
    }

    /**
     * {@inheritDoc}
     *
     * @param  string                 $identifier
     * @param  Profile                $profileId
     * @return Price
     * @throws PriceNotFoundException If the PriceItem was not found.
     */
    public function getPrice($identifier, Profile $profile = null)
    {
        $item = $this->itemMapper->findByIdentifier($identifier);

        if (!$item) {
            throw PriceNotFoundException::itemNotFound($identifier);
        }

        $price = (null === $profile)
            ? $this->getDefaultPrice($item)
            : $this->getActivePrice($item, $profile);

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
    ) {
        $profile = $profile ?: $this->getDefaultProfile();

        $item = $this->itemMapper->findByIdentifier($identifier);

        if (!$item) {
            $item = $this->registerNewItem($identifier, $description);
        }

        $price = new PriceEntity();

        $price->setItem($item);
        $price->setProfile($profile);

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
     * Creates a new price item and persists it to the database.
     *
     * @param  string    $identifier
     * @param  string    $description
     * @return PriceItem
     */
    protected function registerNewItem($identifier, $description)
    {
        $item = new PriceItem();

        $item->setIdentifier($identifier);
        $item->setDescription($description);

        $this->itemMapper->save($item);

        return $item;
    }


    /**
     * Load the default price for the given item.
     *
     * @param  PriceItem $item
     * @return Price|null
     */
    protected function getDefaultPrice(PriceItem $item)
    {
        $profile = $this->getDefaultProfile();

        return $this->priceMapper->findForItemAndProfile($item, $profile);
    }

    /**
     * Returns the price entity for the given item and profile, will go
     * to the default if one is not found.
     *
     * @param  PriceItem $item
     * @param  Profile   $profile
     * @return Price|null
     */
    protected function getActivePrice(PriceItem $item, Profile $profile)
    {
        $price = $this->loadPriceForProfile($item, $profile);

        if (!$price) {
            $price = $this->getDefaultPrice($item);
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
     * @param  PriceItem              $item
     * @param  Profile                $profile
     * @return Price|null
     * @throws PriceNotFoundException If requested profile was not found.
     */
    protected function loadPriceForProfile(PriceItem $item, Profile $profile)
    {
        return $this->priceMapper->findForItemAndProfile($item, $profile);
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
