<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Cache;

class ManagerCache
{
    public function exists($cacheid)
    {
        if (!$cacheid) {
            return false;
        }

        return sql_value("SELECT count(*) FROM `caches` WHERE `cache_id`=&1", 0, $cacheid) == 1;
    }

    public function userMayModify($cacheid)
    {
        global $login;

        $login->verify();

        $cacheOwner = sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`=&1", - 1, $cacheid);

        return $cacheOwner == $login->userid;
    }
}
