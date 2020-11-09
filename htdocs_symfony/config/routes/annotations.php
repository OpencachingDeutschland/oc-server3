<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../../src/Controller/App', 'annotation')
        ->namePrefix('app_');

    $routingConfigurator->import('../../src/Controller/Backend', 'annotation')
        ->namePrefix('backend_')
        ->prefix('/backend');

    $routingConfigurator->import('../../src/Kernel.php', 'annotation');
};
