<?php

namespace SclZfPriceManager;

return array(
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/'. __NAMESPACE__ . '/Entity/',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ => __NAMESPACE__ . '_driver',
                ),
            ),
        ),
    ),

    'scl_zf_price_manager' => array(
        'default_profile' => 1,
    ),
);
