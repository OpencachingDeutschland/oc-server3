<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Libse\Cache;

class ManagerCache
{
    public function exists($cacheId)
    {
        if (!$cacheId) {
            return false;
        }

        return sql_value('SELECT count(*) FROM `caches` WHERE `cache_id`=&1', 0, $cacheId) == 1;
    }

    public function userMayModify($cacheId)
    {
        global $login;

        $login->verify();

        $cacheOwner = sql_value('SELECT `user_id` FROM `caches` WHERE `cache_id`=&1', - 1, $cacheId);

        return $cacheOwner == $login->userid;
    }
}
