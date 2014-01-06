<?php

namespace SclZfPriceManagerTests;

use SclZfPriceManager\Module;
use SclTest\Zf2\AbstractTestCase;

class ModuleTest extends AbstractTestCase
{
    public function test_it_creates_price_service()
    {
        $this->assertServiceIsInstanceOf(
            'SclZfPriceManager\Service\PriceService',
            'SclZfPriceManager\Service\PriceService'
        );
    }
}
