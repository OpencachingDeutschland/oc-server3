<?php

namespace OcLegacy;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Container
{
    public static function get($key)
    {
        self::getContainer()->get($key);
    }

    private static function getContainer()
    {
        $containerFile = __DIR__ . '/../../var/cache2/container.php';

        if (file_exists($containerFile)) {
            require_once $containerFile;
            $container = new \ProjectServiceContainer();
        } else {
            $container = new ContainerBuilder();
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../app/config'));
            $loader->load('services_oc.yml');

            // adding parameters to the container
            $loader->load('parameters.yml');

            $container->compile();

            $dumper = new PhpDumper($container);
            file_put_contents($containerFile, $dumper->dump());
        }

        return $container;
    }
}
