<?php

namespace SclZfPriceManager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * The ZF2 module class for the price manager module.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
            ),
            'factories' => array(
                /*
                'scl_zf_pricemanager.money_factory' => function ($sm) {
                    $factory = \SCL\Currency\MoneyFactory::createDefaultInstance();

                    $factory->setDefaultCurrency(
                        \SCL\Currency\CurrencyFactory::createDefaultInstance()->create('GBP')
                    );
                    return $factory;
                },
                */

                // Options
                'SclZfPriceManager\Options\PriceManagerOptionsInterface' => function ($sm) {
                    return new \SclZfPriceManager\Options\PriceManagerOptions(
                        $sm->get('Config')['scl_zf_price_manager']
                    );
                },

                // Mappers
                'SclZfPriceManager\Mapper\ItemMapperInterface' => function ($sm) {
                    return new \SclZfPriceManager\Mapper\DoctrineItemMapper(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm->get('SclZfGenericMapper\Doctrine\FlushLock')
                    );
                },
                'SclZfPriceManager\Mapper\PriceMapperInterface' => function ($sm) {
                    return new \SclZfPriceManager\Mapper\DoctrinePriceMapper(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm->get('SclZfGenericMapper\Doctrine\FlushLock')
                    );
                },
                'SclZfPriceManager\Mapper\ProfileMapperInterface' => function ($sm) {
                    return new \SclZfPriceManager\Mapper\DoctrineProfileMapper(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm->get('SclZfGenericMapper\Doctrine\FlushLock')
                    );
                },
                'SclZfPriceManager\Mapper\MapperTaxInterface' => function ($sm) {
                    return new \SclZfPriceManager\Mapper\DoctrineTaxMapper(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm->get('SclZfGenericMapper\Doctrine\FlushLock')
                    );
                },
                'SclZfPriceManager\Mapper\VariationMapperInterface' => function ($sm) {
                    return new \SclZfPriceManager\Mapper\DoctrineVariationMapper(
                        $sm->get('doctrine.entitymanager.orm_default'),
                        $sm->get('SclZfGenericMapper\Doctrine\FlushLock')
                    );
                },

                // Services
                'SclZfPriceManager\Service\ItemService' => function ($sm) {
                    return new \SclZfPriceManager\Service\ItemService(
                        $sm->get('SclZfPriceManager\Mapper\ItemMapperInterface')
                    );
                },
                'SclZfPriceManager\Service\PriceService' => function ($sm) {
                    $options = $sm->get('SclZfPriceManager\Options\PriceManagerOptionsInterface');

                    return new \SclZfPriceManager\Service\PriceService(
                        $options->getDefaultProfile(),
                        $sm->get('SclZfPriceManager\Service\VariationService'),
                        $sm->get('SclZfPriceManager\Mapper\ProfileMapperInterface'),
                        $sm->get('SclZfPriceManager\Mapper\PriceMapperInterface'),
                        $sm->get('scl_currency.taxed_price_factory')
                    );
                },
                'SclZfPriceManager\Service\VariationService' => function ($sm) {
                    return new \SclZfPriceManager\Service\VariationService(
                        $sm->get('SclZfPriceManager\Service\ItemService'),
                        $sm->get('SclZfPriceManager\Mapper\VariationMapperInterface')
                    );
                },
            ),
        );
    }
}
