<?php

namespace SclZfPriceManagerTests\Service;

use SclZfPriceManager\Service\PriceService;
use SclZfPriceManager\Entity\Price as PriceEntity;
use SCL\Currency\Currency;
use SCL\Currency\Money;
use SCL\Currency\MoneyFactory;
use SCL\Currency\TaxedPriceFactory;
use SCL\Currency\CurrencyFactory;

/**
 * Unit tests for {@see PriceService}.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class PriceServiceTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_PROFILE = 7;
    const TEST_IDENTIFIER = 'test-indentifier';

    private $itemMapper;

    private $itemService;

    private $variationService;

    private $profileMapper;

    private $priceMapper;

    private $taxRate;

    private $priceFactory;

    private $currency;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->variationService = $this->getMockBuilder('SclZfPriceManager\Service\VariationService')
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->profileMapper = $this->getMock('SclZfPriceManager\Mapper\ProfileMapperInterface');

        $this->priceMapper = $this->getMock('SclZfPriceManager\Mapper\PriceMapperInterface');

        $this->priceFactory = TaxedPriceFactory::createDefaultInstance();

        $this->service = new PriceService(
            self::DEFAULT_PROFILE,
            $this->variationService,
            $this->profileMapper,
            $this->priceMapper,
            $this->priceFactory
        );

        $this->taxRate = $this->getMock('SclZfPriceManager\Entity\TaxRate');

    }

    public function test_implements_PriceServiceInterface()
    {
        $this->assertInstanceOf('SclZfPriceManager\Service\PriceServiceInterface', $this->service);
    }

    /*
     *
     * getPrice()
     *
     */

    public function test_getPrice_returns_TaxedPrice()
    {
        $this->setLoadingExpectations();

        $this->assertInstanceOf('SCL\Currency\TaxedPrice', $this->service->getPrice(self::TEST_IDENTIFIER));
    }

    public function test_getPrice_loads_items_by_identifier()
    {
        $this->setLoadingExpectations();

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function test_getPrice_loads_default_profile_when_no_profile_specified()
    {
        $this->setLoadingExpectations();

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function test_getPrice_loads_correct_price_entity()
    {
        $this->setLoadingExpectations();

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function test_getPrice_returns_price_with_correct_amount()
    {
        $amount     = 5000;
        $taxRate    = 20;
        $taxAmount  = 1000;

        $priceEntity = $this->setLoadingExpectations();

        $priceEntity->setAmount($amount);

        $this->taxRate->expects($this->any())
                      ->method('getRate')
                      ->will($this->returnValue($taxRate));

        $price = $this->service->getPrice(self::TEST_IDENTIFIER);

        $this->assertEquals($amount, $price->getAmount()->getUnits(), 'Amount is incorrect.');
        $this->assertEquals($taxAmount, $price->getTax()->getUnits(), 'Tax amount is incorrect.');
    }

    public function test_getPrice_returns_null_if_price_entity_is_not_found()
    {
        $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->setExpectsToLoadDefaultProfile();

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->will($this->returnValue(null));

        $this->assertNull($this->service->getPrice(self::TEST_IDENTIFIER));
    }

    public function test_getPrice_with_profile_reverts_to_default_if_item_not_found()
    {
        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $profile        = $this->getMock('SclZfPriceManager\Entity\Profile');
        $defaultProfile = $this->getMock('SclZfPriceManager\Entity\Profile');
        $price          = new PriceEntity();

        $price->setTaxRate($this->taxRate);

        $this->priceMapper
             ->expects($this->at(0))
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation))
             ->will($this->returnValue(null));

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue($defaultProfile));

        $this->priceMapper
             ->expects($this->at(1))
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($defaultProfile), $this->identicalTo($variation))
             ->will($this->returnValue($price));

        $this->service->getPrice(self::TEST_IDENTIFIER, null, $profile);
    }

    public function test_getPrice_throws_when_variation_not_found()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\PriceNotFoundException');

        $this->variationService
             ->expects($this->once())
             ->method('getVariation')
             ->will($this->returnValue(null));

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    public function test_getPrice_throws_when_default_profile_is_not_found()
    {
        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->setExpectedException(
            'SclZfPriceManager\Exception\PriceNotFoundException',
            'The default profile was not found'
        );

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue(null));

        $this->service->getPrice(self::TEST_IDENTIFIER);
    }

    /*
     *
     * savePrice()
     *
     */

    public function test_savePrice_returns_price_entity()
    {
        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $amount = $this->createMoney(2100);

        $this->assertInstanceOf(
            'SclZfPriceManager\Entity\Price',
            $this->service->savePrice(self::TEST_IDENTIFIER, $amount, $this->taxRate)
        );
    }

    public function test_savePrice_fetches_variation()
    {
        $itemIdentifier       = 'item-id';
        $variationIdentifier  = 'var-id';
        $itemDescription      = 'item-desc';
        $variationDescription = 'var-desc';

        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToGetVariation(
            $itemIdentifier,
            $variationIdentifier,
            $itemDescription,
            $variationDescription
        );

        $this->service->savePrice(
            $itemIdentifier,
            $this->createMoney(10000),
            $this->taxRate,
            $variationIdentifier,
            $variationDescription,
            $itemDescription
        );
    }

    public function test_savePrice_loads_default_profile_when_no_profile_is_given()
    {
        $this->setExpectsToLoadDefaultProfile();

        $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney(42),
            $this->taxRate
        );
    }

    public function test_savePrice_throws_when_default_profile_cannot_be_found()
    {
        $this->setExpectedException('SclZfPriceManager\Exception\PriceNotFoundException');

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue(null));

        $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney(42),
            $this->taxRate
        );
    }

    public function test_savePrice_loads_price()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation));

        $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney(42),
            $this->taxRate
        );
    }

    public function test_savePrice_entity_contains_correct_values()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $price = $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney(105),
            $this->taxRate
        );

        $this->assertSame($profile, $price->getProfile(), 'Profile is incorrect.');
        $this->assertSame($variation, $price->getVariation(), 'Variation is incorrect');
    }

    public function test_savePrice_entity_uses_given_profile()
    {
        $profile = $this->getMock('SclZfPriceManager\Entity\Profile');

        $item = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $price = $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney(105),
            $this->taxRate,
            null,
            '',
            '',
            $profile
        );

        $this->assertSame($profile, $price->getProfile(), 'Profile is incorrect.');
    }

    public function test_savePrice_saves_entity()
    {
        $amount = 105;

        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $priceToSave = new \SclZfPriceManager\Entity\Price();
        $priceToSave->setVariation($variation);
        $priceToSave->setProfile($profile);
        $priceToSave->setTaxRate($this->taxRate);
        $priceToSave->setAmount($amount);

        $this->priceMapper
             ->expects($this->once())
             ->method('save')
             ->with($this->equalTo($priceToSave));

        $price = $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney($amount),
            $this->taxRate
        );
    }

    public function test_savePrice_loads_existing_price_if_it_exists()
    {
        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation));

        $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney(201),
            $this->taxRate
        );
    }

    public function test_savePrice_updates_existing_price_amount()
    {
        $amount = 201;

        $price = new \SclZfPriceManager\Entity\Price();

        $profile = $this->setExpectsToLoadDefaultProfile();

        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation))
             ->will($this->returnValue($price));

        $result = $this->service->savePrice(
            self::TEST_IDENTIFIER,
            $this->createMoney($amount),
            $this->taxRate
        );

        $this->assertSame($price, $result);

        $this->assertSame($this->taxRate, $result->getTaxRate());
        $this->assertEquals($amount, $result->getAmount());
    }

    /*
     * Protected methods
     */

    protected function setLoadingExpectations()
    {
        $variation = $this->setExpectsToGetVariation(self::TEST_IDENTIFIER);

        $profile = $this->setExpectsToLoadDefaultProfile();

        return $this->setExpectsToLoadPrice($variation, $profile);
    }

    protected function setExpectsToGetVariation($itemId, $varId = null, $varDesc = '', $itemDesc = '')
    {
        $variation = $this->getMock('SclZfPriceManager\Entity\Variation');

        $this->variationService
             ->expects($this->once())
             ->method('getVariation')
             ->with(
                $this->equalTo($itemId),
                $this->equalTo($varId),
                $this->equalTo($varDesc),
                $this->equalTo($itemDesc)
             )
             ->will($this->returnValue($variation));

        return $variation;
    }

    protected function setExpectsToLoadDefaultProfile()
    {
        $profile = $this->getMock('SclZfPriceManager\Entity\Profile');

        $this->profileMapper
             ->expects($this->once())
             ->method('findById')
             ->with($this->equalTo(self::DEFAULT_PROFILE))
             ->will($this->returnValue($profile));

        return $profile;
    }

    protected function setExpectsToLoadPrice($variation, $profile)
    {
        $priceEntity = new PriceEntity();
        $priceEntity->setTaxRate($this->taxRate);

        $this->priceMapper
             ->expects($this->once())
             ->method('findByProfileAndVariation')
             ->with($this->identicalTo($profile), $this->identicalTo($variation))
             ->will($this->returnValue($priceEntity));

        return $priceEntity;
    }

    private function createMoney($amount)
    {
        return new Money($amount, $this->priceFactory->getDefaultCurrency());
    }
}
