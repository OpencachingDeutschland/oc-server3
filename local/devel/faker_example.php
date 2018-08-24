<?php

use Doctrine\DBAL\Connection;

require_once __DIR__ . '/../../htdocs/lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$faker = Faker\Factory::create();

echo $faker->latitude;
