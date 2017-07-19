<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\GeoCache;

class StatisticPicture
{
    public static function deleteStatisticPicture($userId)
    {
        $userId += 0;

        // data changed - delete stat pic of user, if exists - will be recreated on next request
        $imagePath = __DIR__ . '/../../../images/statpics/statpic' . $userId . '.jpg';
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
