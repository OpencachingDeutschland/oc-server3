<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Libse\Cache;

use Doctrine\DBAL\Connection;

class ManagerCache
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function exists($cacheId)
    {
        if (!$cacheId) {
            return false;
        }

        return $this->connection
                ->fetchColumn(
                    'SELECT COUNT(*) FROM `caches` WHERE `cache_id` = :cacheId',
                    ['cacheId' => $cacheId]
                ) == 1;
    }

    public function userMayModify($cacheId)
    {
        global $login;

        $login->verify();

        $cacheOwner = sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`=&1", -1, $cacheId);

        return $cacheOwner == $login->userid;
    }
}
