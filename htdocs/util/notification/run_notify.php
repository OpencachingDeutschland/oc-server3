#!/usr/local/bin/php -q
<?php
/***************************************************************************
 * For license information see LICENSE.md
 *
 *
 * Processes the `notify_waiting` table and sends notification mails
 * on new caches and new OConly attributes.
 ***************************************************************************/

// needs absolute rootPath because called as cronjob
use Oc\Util\ProcessSync;

$rootPath = __DIR__ . '/../../';

require_once $rootPath . 'lib/clicompatbase.inc.php';
require_once $rootPath . 'lib2/translate.class.php';
require_once __DIR__ . '/settings.inc.php';
require_once $rootPath . 'lib/consts.inc.php';
require_once $rootPath . 'lib2/logic/geomath.class.php';

if (!Cronjobs::enabled()) {
    exit;
}

// db connect
db_connect();
if ($dblink === false) {
    echo 'Unable to connect to database';
    exit;
}

// These functions are needed for notification emails in the recipient's
// language. Looks like there is no way to query this "inline" in LEFT-JOIN-
// statements (the ON clause cannot access user.language).

/**
 * @param mixed $cacheType
 * @param mixed $language
 * @return string
 */
function getCacheTypeName($cacheType, $language)
{
    return sqlValue(
        "SELECT IFNULL(`stt`.`text`, `cache_type`.`en`)
         FROM `cache_type`
         LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`cache_type`.`trans_id` AND `stt`.`lang`='" . sql_escape($language) . "'
         WHERE `cache_type`.`id`='" . sql_escape($cacheType) . "'",
        ''
    );
}

/**
 * @param mixed $cacheSize
 * @param mixed $language
 * @return string
 */
function getCacheSizeName($cacheSize, $language)
{
    return sqlValue(
        "SELECT IFNULL(`stt`.`text`, `cache_size`.`en`)
         FROM `cache_size`
         LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`cache_size`.`trans_id` AND `stt`.`lang`='" . sql_escape($language) . "'
         WHERE `cache_size`.`id`='" . sql_escape($cacheSize) . "'",
        ''
    );
}

$processSync = new ProcessSync('run_notify');
if ($processSync->enter()) {
    // send out everything that has to be sent
    $rsNotify = sql(
        "SELECT
            `notify_waiting`.`id`, `notify_waiting`.`cache_id`, `notify_waiting`.`type`,
            `user`.`username`,
            `user2`.`email`, `user2`.`username` AS `recpname`, `user2`.`latitude` AS `lat1`,
            `user2`.`longitude` AS `lon1`, `user2`.`user_id` AS `recid`,
            IFNULL(`user2`.`language`,'&1') AS `recp_lang`,
            `user2`.`domain` AS `recp_domain`,
            `caches`.`name` AS `cachename`, `caches`.`latitude`
            AS `lat2`, `caches`.`longitude`
            AS `lon2`, `caches`.`wp_oc`,
            `caches`.`date_hidden`,
            `caches`.`type` AS `cachetype`, `caches`.`size` AS `cachesize`,
            `cache_status`.`allow_user_view`,
            `cache_ignore`.`cache_id` IS NOT NULL AS `ignored`,
            `ca`.`attrib_id` IS NOT NULL AS `oconly`
        FROM `notify_waiting`
        INNER JOIN `caches` ON `notify_waiting`.`cache_id`=`caches`.`cache_id`
        INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
        INNER JOIN `user` `user2` ON `notify_waiting`.`user_id`=`user2`.`user_id`
        INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
        INNER JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id`
        INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
        LEFT JOIN `cache_ignore` ON `cache_ignore`.`cache_id`=`caches`.`cache_id` AND `cache_ignore`.`user_id`=`notify_waiting`.`user_id`
        LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6",
        $opt['template']['default']['locale']
    );

    while ($rNotify = sql_fetch_array($rsNotify)) {
        if ($rNotify['allow_user_view'] && !$rNotify['ignored']) {
            process_new_cache($rNotify);
        }
        sql("DELETE FROM `notify_waiting` WHERE `id` ='&1'", $rNotify['id']);
    }
    mysqli_free_result($rsNotify);

    $processSync->leave();
}


function process_new_cache($notify)
{
    global $opt, $debug, $debug_mailto, $rootPath, $translate;
    global $maildomain, $mailfrom;

    //echo "process_new_cache(".$notify['id'].")\n";
    $error = false;

    // fetch email template
    switch ($notify['type']) {
        case NOTIFY_NEW_CACHE: // Type: new cache
            $mailBody = fetch_email_template('notify_newcache', $notify['recp_lang'], $notify['recp_domain']);
            $mailsubject = '[' . $maildomain . '] ' .
                $translate->t(
                    $notify['oconly'] ? 'New OConly cache:' : 'New cache:',
                    '',
                    basename(__FILE__),
                    __LINE__,
                    '',
                    1,
                    $notify['recp_lang']
                ) .
                ' ' . $notify['cachename'];
            break;

        case NOTIFY_NEW_OCONLY: // Type: new OConly flag
            $mailBody = fetch_email_template('notify_newoconly', $notify['recp_lang'], $notify['recp_domain']);
            $mailsubject = '[' . $maildomain . '] ' .
                $translate->t(
                    'Cache was marked as OConly:',
                    '',
                    basename(__FILE__),
                    __LINE__,
                    '',
                    1,
                    $notify['recp_lang']
                ) .
                ' ' . $notify['cachename'];
            break;

        default:
            $error = true;
            break;
    }

    if (!$error) {
        $mailBody = mb_ereg_replace('{username}', $notify['recpname'], $mailBody);
        $mailBody = mb_ereg_replace(
            '{date}',
            date($opt['locale'][$notify['recp_lang']]['format']['phpdate'], strtotime($notify['date_hidden'])),
            $mailBody
        );
        $mailBody = mb_ereg_replace('{cacheid}', $notify['cache_id'], $mailBody);
        $mailBody = mb_ereg_replace('{wp_oc}', $notify['wp_oc'], $mailBody);
        $mailBody = mb_ereg_replace('{user}', $notify['username'], $mailBody);
        $mailBody = mb_ereg_replace('{cachename}', $notify['cachename'], $mailBody);
        $mailBody = mb_ereg_replace(
            '{distance}',
            round(geomath::calcDistance($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'], 1), 1),
            $mailBody
        );
        $mailBody = mb_ereg_replace('{unit}', 'km', $mailBody);
        $mailBody = mb_ereg_replace(
            '{bearing}',
            geomath::Bearing2Text(
                geomath::calcBearing(
                    $notify['lat1'],
                    $notify['lon1'],
                    $notify['lat2'],
                    $notify['lon2']
                ),
                0,
                $notify['recp_lang']
            ),
            $mailBody
        );
        $mailBody = mb_ereg_replace(
            '{cachetype}',
            getCacheTypeName($notify['cachetype'], $notify['recp_lang']),
            $mailBody
        );
        $mailBody = mb_ereg_replace(
            '{cachesize}',
            getCacheSizeName($notify['cachesize'], $notify['recp_lang']),
            $mailBody
        );
        $mailBody = mb_ereg_replace(
            '{oconly-}',
            $notify['oconly'] ? $translate->t(
                'OConly-',
                '',
                basename(__FILE__),
                __LINE__,
                '',
                1,
                $notify['recp_lang']
            ) : '',
            $mailBody
        );

        /* begin send out everything that has to be sent */
        $email_headers = 'From: "' . $mailfrom . '" <' . $mailfrom . '>';

        // send email
        if ($debug == true) {
            $mailadr = $debug_mailto;
        } else {
            $mailadr = $notify['email'];
        }

        if (is_existent_maildomain(getToMailDomain($mailadr))) {
            mb_send_mail($mailadr, $mailsubject, $mailBody, $email_headers);
        }
    } else {
        echo 'Unknown notification type: ' . $notify['type'] . '<br />';
    }

    // logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
    logentry('notify_newcache', 8, $notify['recid'], $notify['cache_id'], 0, 'Sending mail to ' . $mailadr, []);

    return 0;
}

/**
 * @param string $domain
 */
function is_existent_maildomain($domain)
{
    $smtp_serverlist = [];
    $smtp_serverweight = [];

    if (getmxrr($domain, $smtp_serverlist, $smtp_serverweight) != false) {
        if (count($smtp_serverlist) > 0) {
            return true;
        }
    }

    // check if A exists
    $a = dns_get_record($domain, DNS_A);
    if (count($a) > 0) {
        return true;
    }

    return false;
}

function getToMailDomain($mail)
{
    if ($mail == '') {
        return '';
    }

    if (strrpos($mail, '@') === false) {
        $domain = 'localhost';
    } else {
        $domain = substr($mail, strrpos($mail, '@') + 1);
    }

    return $domain;
}
