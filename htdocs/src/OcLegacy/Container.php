<?php

namespace OcLegacy;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Container
{
    private static $container = false;

    public static function get($key)
    {
        self::getContainer()->get($key);
    }

    private static function getContainer()
    {
        if (!self::$container) {
            $container = new ContainerBuilder();
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../app/config'));
            $loader->load('services_oc.yml');

            // adding parameters to the container
            $loader->load('parameters.yml');

            $container->compile();


            self::$container = $container;
        }

        return self::$container;
    }
}
