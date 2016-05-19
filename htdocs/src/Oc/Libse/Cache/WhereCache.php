<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Cache;

class WhereCache
{
    public static function active()
    {
        return ' caches.status = ' . StatusCache::ACTIVE.' AND (caches.date_activate IS NULL OR caches.date_activate <= NOW()) ';
    }

    public static function publishNow()
    {
        return ' caches.status = ' . StatusCache::NOT_YET_PUBLISHED.' AND caches.date_activate <= NOW() ';
    }
}
