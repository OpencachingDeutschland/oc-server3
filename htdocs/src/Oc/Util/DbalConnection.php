<?php
/***************************************************************************
 * For license information see LICENSE.md
 * small helper class to get a dbal connection or dbal query builder
 * to refactor sql methods
 ***************************************************************************/

namespace Oc\Util;

use Doctrine\DBAL\DriverManager;

/**
 * Class DbalConnection
 *
 * @package Oc\Util
 */
class DbalConnection
{
    /**
     * Creates dbal connection.
     *
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $password
     * @param int $port
     *
     * @return \Doctrine\DBAL\Connection
     */
    public static function createDbalConnection(
        $host,
        $name,
        $user,
        $password,
        $port = null
    ) {
        $params = [];
        $params['driver'] = 'pdo_mysql';

        if ($host) {
            $params['host'] = $host;
        }

        if ($name) {
            $params['dbname'] = $name;
        }

        if ($user) {
            $params['user'] = $user;
        }

        if ($password) {
            $params['password'] = $password;
        }

        if ($port !== null) {
            $params['port'] = $port;
        }

        return DriverManager::getConnection($params);
    }
}
