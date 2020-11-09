<?php

declare(strict_types=1);

use Oc\Security\RoleHierarchyFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
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
            __DIR__ . '/../src/Entity/',
            __DIR__ . '/../src/Kernel.php',
            __DIR__ . '/../src/Tests/'
        ]);

    $services->load('Oc\Controller\\', __DIR__ . '/../src/Controller/')
        ->tag('controller.service_arguments');

    $services->load('Oc\Command\\', __DIR__ . '/../src/Command/')
        ->tag('console.command');

    $services->set('security.role_hierarchy', RoleHierarchyInterface::class)
        ->factory([service(RoleHierarchyFactory::class), 'create']);
};
