<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 ***
 * Smarty {season winter='...' spring='...' summer='...' autumn='...'} function plugin
 */
function smarty_function_season($params, &$smarty)
{
    $seasons = [];

    // http://www.usno.navy.mil
    $seasons[] = [
        '2010-03-20 17:32:00 GMT',
        '2010-06-21 11:28:00 GMT',
        '2010-09-23 03:09:00 GMT',
        '2010-12-21 23:38:00 GMT'
    ];
    $seasons[] = [
        '2011-03-20 23:21:00 GMT',
        '2011-06-21 17:16:00 GMT',
        '2011-09-23 09:05:00 GMT',
        '2011-12-22 05:30:00 GMT'
    ];
    $seasons[] = [
        '2012-03-20 05:14:00 GMT',
        '2012-06-20 23:09:00 GMT',
        '2012-09-22 14:49:00 GMT',
        '2012-12-21 11:12:00 GMT'
    ];
    $seasons[] = [
        '2013-03-20 11:02:00 GMT',
        '2013-06-21 05:04:00 GMT',
        '2013-09-22 20:44:00 GMT',
        '2013-12-21 17:11:00 GMT'
    ];
    $seasons[] = [
        '2014-03-20 16:57:00 GMT',
        '2014-06-21 10:51:00 GMT',
        '2014-09-23 02:29:00 GMT',
        '2014-12-21 23:03:00 GMT'
    ];
    $seasons[] = [
        '2015-03-20 22:45:00 GMT',
        '2015-06-21 16:38:00 GMT',
        '2015-09-23 08:21:00 GMT',
        '2015-12-22 04:48:00 GMT'
    ];
    $seasons[] = [
        '2016-03-20 04:30:00 GMT',
        '2016-06-20 22:34:00 GMT',
        '2016-09-22 14:21:00 GMT',
        '2016-12-21 10:44:00 GMT'
    ];
    $seasons[] = [
        '2017-03-20 10:29:00 GMT',
        '2017-06-21 04:24:00 GMT',
        '2017-09-22 20:02:00 GMT',
        '2017-12-21 16:28:00 GMT'
    ];
    $seasons[] = [
        '2018-03-20 16:15:00 GMT',
        '2018-06-21 10:07:00 GMT',
        '2018-09-23 01:54:00 GMT',
        '2018-12-21 22:23:00 GMT'
    ];
    $seasons[] = [
        '2019-03-20 21:58:00 GMT',
        '2019-06-21 15:54:00 GMT',
        '2019-09-23 07:50:00 GMT',
        '2019-12-22 04:19:00 GMT'
    ];
    $seasons[] = [
        '2020-03-20 03:50:00 GMT',
        '2020-06-20 21:44:00 GMT',
        '2020-09-22 13:31:00 GMT',
        '2020-12-21 10:02:00 GMT'
    ];

    $nTimestamp = time();
    for ($nIndex = 0; $nIndex < count($seasons); $nIndex ++) {
        if (strtotime($seasons[$nIndex][0]) > $nTimestamp) {
            return $params['winter'];
        } //'';
        else {
            if (strtotime($seasons[$nIndex][1]) > $nTimestamp) {
                return $params['spring'];
            } //'resource2/ocstyle/css/seasons/style_spring.css';
            else {
                if (strtotime($seasons[$nIndex][2]) > $nTimestamp) {
                    return $params['summer'];
                } //'resource2/ocstyle/css/seasons/style_summer.css';
                else {
                    if (strtotime($seasons[$nIndex][3]) > $nTimestamp) {
                        return $params['autumn'];
                    }
                }
            }
        } //'resource2/ocstyle/css/seasons/style_autumn.css';
    }

    return '';
}
