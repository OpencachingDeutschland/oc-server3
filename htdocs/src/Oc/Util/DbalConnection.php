<?php
/***************************************************************************
 * For license information see LICENSE.md
 *
 * small helper class to get a dbal connection or dbal query builder
 * to refactor sql methods
 ***************************************************************************/

namespace Oc\Util;

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Yaml\Yaml;

class DbalConnection
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct()
    {
        $params = [];
        $params['driver'] = 'pdo_mysql';

        $parameters = Yaml::parse(file_get_contents(__DIR__ . '/../../../app/config/parameters.yml'));
        $parameters = $parameters['parameters'];

        if (isset($parameters['database_host'])) {
            $params['host'] = $parameters['database_host'];
        }

        if (isset($parameters['database_port'])) {
            $params['port'] = $parameters['database_port'];
        }

        if (isset($parameters['database_user'])) {
            $params['user'] = $parameters['database_user'];
        }

        if (isset($parameters['database_password'])) {
            $params['password'] = $parameters['database_password'];
        }

        if (isset($parameters['database_name'])) {
            $params['dbname'] = $parameters['database_name'];
        }

        $this->connection = DriverManager::getConnection($params);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->connection->createQueryBuilder();
    }
}
