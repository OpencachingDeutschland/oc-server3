<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Libse\CacheNote;

use Oc\Libse\Coordinate\TypeCoordinate;

class HandlerCacheNote
{
    public function getCacheNote($userId, $cacheId)
    {
        $rs = sql(
            "SELECT id, latitude, longitude, description
             FROM coordinates
             WHERE user_id = &1
             AND cache_id = &2
             AND type = &3",
            $userId,
            $cacheId,
            TypeCoordinate::UserNote
        );
        $ret = $this->recordToArray(sql_fetch_array($rs));
        mysql_free_result($rs);

        return $ret;
    }

    private function recordToArray($r)
    {
        $ret = [];

        $ret['id'] = $r['id'];
        $ret['note'] = $r['description'];
        $ret['latitude'] = $r['latitude'];
        $ret['longitude'] = $r['longitude'];

        return $ret;
    }

    public function save($noteId, $userId, $cacheId, $note, $latitude, $longitude)
    {
        if (!$note && !$latitude && !$longitude) {
            sql(
                "DELETE FROM coordinates
                 WHERE user_id = &1
                 AND cache_id = &2
                 AND type = &3",
                $userId,
                $cacheId,
                TypeCoordinate::UserNote
            );
        } elseif (!$noteId) {
            sql(
                "INSERT INTO coordinates(type, latitude, longitude, cache_id, user_id, description)
                 VALUES(&1, &2, &3, &4, &5, '&6')",
                TypeCoordinate::UserNote,
                $latitude,
                $longitude,
                $cacheId,
                $userId,
                $note
            );
        } else {
            sql(
                "UPDATE coordinates
                 SET latitude = &1, longitude = &2, description = '&3'
                 WHERE id = &4
                 AND user_id = &5
                 AND cache_id = &6
                 AND type = &7",
                $latitude,
                $longitude,
                $note,
                $noteId,
                $userId,
                $cacheId,
                TypeCoordinate::UserNote
            );
        }
    }
}
