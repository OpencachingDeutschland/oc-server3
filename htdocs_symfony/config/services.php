<?php

declare(strict_types=1);

use Oc\Menu\MenuGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('Oc\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Kernel.php',
            __DIR__ . '/../src/Tests/'
        ]);

    $services->load('Oc\Controller\\', __DIR__ . '/../src/Controller/')
        ->tag('controller.service_arguments');

    $services->load('Oc\Command\\', __DIR__ . '/../src/Command/')
        ->tag('console.command')
        ->public();

    $services->set('app.menu_builder', MenuGenerator::class)
            ->args([
                    service('knp_menu.factory')
            ])
            ->tag('knp_menu.menu_builder', [
                    'method' => 'createSideMenu',
                    'alias'  => 'sideMenu'
            ]);
};
