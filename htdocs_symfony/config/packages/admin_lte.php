<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('admin_lte', [
        'options' => [
            'skin' => 'skin-yellow',
            'fixed_layout' => true,
            'boxed_layout' => true,
            'collapsed_sidebar' => false,
            'mini_sidebar' => true,
        ],
        'knp_menu' => [
            'enable' => true,
//            'main_menu' => 'backend_main',
            'breadcrumb_menu' => true,
        ],
        'routes' => [
            'adminlte_welcome' => 'backend_index_index',
        ],
    ]);
};
