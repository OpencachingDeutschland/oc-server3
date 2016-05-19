<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\CacheNote;

use Oc\Libse\Coordinate\TypeCoordinate;

class HandlerCacheNote
{
    public function getCacheNote($userid, $cacheid)
    {
        $rs = sql("SELECT id, latitude, longitude, description FROM coordinates WHERE user_id = &1 AND cache_id = &2 AND type = &3", $userid, $cacheid, TypeCoordinate::USER_NOTE);
        $ret = $this->recordToArray(sql_fetch_array($rs));
        mysql_free_result($rs);

        return $ret;
    }

    private function recordToArray($r)
    {
        $ret = array();

        $ret['id'] = $r['id'];
        $ret['note'] = $r['description'];
        $ret['latitude'] = $r['latitude'];
        $ret['longitude'] = $r['longitude'];

        return $ret;
    }

    public function save($noteid, $userid, $cacheid, $note, $latitude, $longitude)
    {
        if (!$note && !$latitude && !$longitude) {
            sql(
                "DELETE FROM coordinates
                WHERE user_id = &1
                AND cache_id = &2
                AND type = &3",
                $userid,
                $cacheid,
                TypeCoordinate::USER_NOTE
            );
        } elseif (!$noteid) {
            sql(
                "INSERT INTO coordinates(type, latitude, longitude, cache_id, user_id, description)
                VALUES(&1, &2, &3, &4, &5, '&6')",
                TypeCoordinate::USER_NOTE,
                $latitude,
                $longitude,
                $cacheid,
                $userid,
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
                $noteid,
                $userid,
                $cacheid,
                TypeCoordinate::USER_NOTE
            );
        }

    }
}
