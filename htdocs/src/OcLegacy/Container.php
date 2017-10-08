<?php

namespace OcLegacy;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Container
{
    public static function get($key)
    {
        return self::getContainer()->get($key);
    }

    /**
     * @return Container
     */
    private static function getContainer()
    {
        $containerFile = __DIR__ . '/../../var/cache2/container.php';

        $containerConfigCache = new ConfigCache(
            $containerFile,
            false
        );

        if (!$containerConfigCache->isFresh()) {
            self::generateContainer($containerConfigCache);
        }

        require_once $containerFile;

        return new \OcLegacyContainer();
    }

    /**
     * @param ConfigCache $containerConfigCache
     * @return ContainerBuilder
     */
    private static function generateContainer(ConfigCache $containerConfigCache)
    {
        $container = new ContainerBuilder();
        $fileLocator = new FileLocator(__DIR__ . '/../../app/config');
        $loader = new XmlFileLoader($container, $fileLocator);
        $loader->load('services_oc.xml');

        $loader = new YamlFileLoader($container, $fileLocator);
        $loader->load('parameters.yml');

        $container->compile();

        $dumper = new PhpDumper($container);
        $containerConfigCache->write(
            $dumper->dump(['class' => 'OcLegacyContainer']),
            $container->getResources()
        );
    }
}
