<?php

return array(
    'modules' => array(
        'SCL\\ZF2\\Currency',
        'SclZfPriceManager',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config.php',
        ),
        'module_paths' => array(
            __DIR__ . '/../..',
            __DIR__ . '/../vendor',
        ),
    ),
);
