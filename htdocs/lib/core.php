<?php

/****************************************************************************
                               _    _                _
 ___ _ __  ___ _ _  __ __ _ __| |_ (_)_ _  __ _   __| |___
/ _ \ '_ \/ -_) ' \/ _/ _` / _| ' \| | ' \/ _` |_/ _` / -_)
\___/ .__/\___|_||_\__\__,_\__|_||_|_|_||_\__, (_)__,_\___|
    |_|                                   |___/

For license information see doc/license.txt   ---   Unicode Reminder メモ

****************************************************************************/

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config/'));
$loader->load('services.yml');
// $loader->load('anotherfile.yml');

$config = $container->get('ocde.config');
