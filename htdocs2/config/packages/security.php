<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'providers' => [
            'users_in_memory' => [
                'memory' => null
            ]
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false
            ],
            'main' => [
                'anonymous' => true,
                'lazy' => true,
                'provider' => 'users_in_memory'
            ]
        ],
        'access_control' => null,
    ]);
};
