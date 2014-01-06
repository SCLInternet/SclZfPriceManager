<?php

namespace SclZfPriceManagerTests;

class classTest extends \PHPUnit_Framework_TestCase
{
    public function test_MoneyFactory_is_created()
    {
        $this->assertInstanceOf(
            'SCL\Currency\MoneyFactory',
            $this->getMoneyFactory()
        );
    }

    public function test_MoneyFactory_has_default_currency()
    {
        $money = $this->getMoneyFactory()->createFromValue(1);

        $this->assertInstanceOf(
            'SCL\Currency\Currency',
            $money->getCurrency()
        );
    }

    public function test_MoneyFactory_default_currency_is_GBP()
    {
        $money = $this->getMoneyFactory()->createFromValue(1);

        $this->assertEquals('GBP', $money->getCurrency()->getCode());
    }

    private function getMoneyFactory()
    {
        return $this->getServiceManager()
                    ->get('scl_zf_pricemanager.money_factory');
    }

    private function getServiceManager()
    {
       return \TestBootstrap::getApplication()->getServiceManager();
    }
}
