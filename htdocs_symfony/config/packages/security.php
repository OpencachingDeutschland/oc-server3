<?php

declare(strict_types=1);

use Oc\Security\LoginFormAuthenticator;
use Oc\Security\UserProvider;
use Oc\Entity\UserEntity;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'enable_authenticator_manager' => true,
        'providers' => [
            'users' => [
                'id' => UserProvider::class,
            ],
        ],
        // Hack for our database role hierarchy
        'role_hierarchy' => ['ROLE_USER' => 'ROLE_USER'],
        'encoders' => [
            UserEntity::class => [
                'algorithm' => 'md5',
                'encode_as_base64' => false,
                'iterations' => 0,
            ]
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'users',
                'logout' => [
                    'path' => 'app_security_logout',
                    // where to redirect after logout
                    'target' => 'app_index_index'
                ],
                'guard' => [
                    'authenticators' => [
                        LoginFormAuthenticator::class,
                    ]
                ],
            ]
        ],
        'access_control' => [
            [
                'path' => '^/backend',
                'roles' => [
                    'ROLE_TEAM',
                ]
            ]
        ],
    ]);
};
