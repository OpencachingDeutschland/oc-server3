<?php

declare(strict_types=1);

use Oc\Security\LegacyCookieAuthenticator;
use Oc\Security\LoginFormAuthenticator;
use Oc\Security\UserProvider;
use Oc\Entity\UserEntity;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function(ContainerConfigurator $containerConfigurator)
: void {
    $containerConfigurator->extension('security', [
        'providers' => [
            'users' => [
                'id' => UserProvider::class,
            ],
        ],
        // Hack for our database role hierarchy
        'role_hierarchy' => ['ROLE_USER' => 'ROLE_USER'],
        'password_hashers' => [
            UserEntity::class => [
                'algorithm' => 'md5',
                'encode_as_base64' => false,
                'iterations' => 0,
            ]
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js|fonts)/',
                'security' => false
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'users',
                'logout' => [
                    'path' => 'app_security_logout',
                    'target' => 'app_security_login',
                ],
                'custom_authenticators' => [
                    LegacyCookieAuthenticator::class,
                    LoginFormAuthenticator::class,
                ],
                'entry_point' => LoginFormAuthenticator::class,
            ]
        ],
        // Order matters: public routes first, catch-all last.
        'access_control' => [
            ['path' => '^/security/',  'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/login',      'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/logout',     'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/register',   'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/locale/',    'roles' => 'PUBLIC_ACCESS'],
            ['path' => '^/backoffice', 'roles' => ['ROLE_TEAM']],
            ['path' => '^/',           'roles' => 'IS_AUTHENTICATED_FULLY'],
        ],
    ]);
};
