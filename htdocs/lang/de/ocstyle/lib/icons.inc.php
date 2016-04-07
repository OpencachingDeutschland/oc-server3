<?php
/****************************************************************************
 * begin                : Fr Sept 9 2005
 *
 * For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
 *
 * Unicode Reminder メモ
 *
 * set template specific variables for icons
 ****************************************************************************/

function icon_log_type($icon_small, $text)
{
    global $stylepath;

    return "<img src='resource2/ocstyle/images/$icon_small' width='16' height='16' align='middle' border='0' align='left' alt='' title='$text' />";
}
