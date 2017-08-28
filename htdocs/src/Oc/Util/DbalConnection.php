<?php
/***************************************************************************
 * For license information see LICENSE.md
 * small helper class to get a dbal connection or dbal query builder
 * to refactor sql methods
 ***************************************************************************/

namespace Oc\Util;

use Doctrine\DBAL\DriverManager;

class DbalConnection
{
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

        if ($port) {
            $params['port'] = $port;
        }

        return DriverManager::getConnection($params);
    }
}
