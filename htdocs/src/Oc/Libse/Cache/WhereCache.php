<?php

namespace Oc\Libse\Cache;

class WhereCache
{
    public static function active()
    {
        return ' caches.status = ' . StatusCache::Active . ' AND (caches.date_activate IS NULL OR caches.date_activate <= NOW()) ';
    }

    public static function publishNow()
    {
        return ' caches.status = ' . StatusCache::NotYetPubliced . ' AND caches.date_activate <= NOW() ';
    }
}
