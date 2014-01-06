<?php
/**
 * SclZfGenericMapper (https://github.com/SCLInternet/SclZfGenericMapper)
 *
 * @link https://github.com/SCLInternet/SclZfGenericMapper for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

class TestBootstrap
{
    private static $autoloaderFiles = [
        '../vendor/autoload.php',
    ];

    private static $application;

    /**
     * Setup the testing environment.
     *
     * @param  string $config Path to the Zend application config file.
     * @return void
     */
    public static function init($config)
    {
        $loader = self::getAutoloader();

        $loader->add('SclZfPriceManagerTests\\', __DIR__);

        self::$application = \Zend\Mvc\Application::init($config);
    }

    /**
     * Return the application instance.
     *
     * @return \Zend\Mvc\Application
     */
    public static function getApplication()
    {
        return self::$application;
    }

    private static function getAutoloader()
    {
        global $loader;

        foreach (self::$autoloaderFiles as $file) {
            if ($file[1] !== '/') {
                $file = __DIR__ . '/' . $file;
            }


            if (file_exists($file)) {
                $loader = include $file;

                break;
            }
        }

        if (!isset($loader) || !$loader) {
            throw new \RuntimeException('vendor/autoload.php not found. Have you run composer?');
        }

        return $loader;
    }
}

TestBootstrap::init(include __DIR__ . '/application.config.php');
