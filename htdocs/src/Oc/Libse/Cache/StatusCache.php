<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\Libse\Cache;

class StatusCache
{
    const ACTIVE = 1;
    const TEMP_UNAVAILABLE = 2;
    const ARCHIVED = 3;
    const TO_BE_APPROVED = 4;
    const NOT_YET_PUBLISHED = 5;
    const BLOCKED = 6;
    const BLOCKED_HIDDEN = 7;
}
