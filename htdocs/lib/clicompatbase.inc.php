<?php
/****************************************************************************
 * ./lib/clicompatbase.inc.php
 * --------------------
 * begin                : Fri September 16 2005
 * For license information see LICENSE.md
 ****************************************************************************/

use Oc\Util\CBench;

/****************************************************************************
 * contains functions that are compatible with the php-CLI-scripts under util.
 * Can be included without including common.inc.php, but will be included from
 * common.inc.php.
 * Global variables that need to be set up when including without common.inc.php:
 * $dblink
 ****************************************************************************/

require_once __DIR__ . '/../vendor/autoload.php';

global $interface_output, $dblink_slave;
if (!isset($interface_output)) {
    $interface_output = 'plain';
}

if (isset($opt['rootpath'])) {
    $rootpath = $opt['rootpath'];
} elseif (isset($rootpath)) {
    $opt['rootpath'] = $rootpath;
} else {
    $rootpath = __DIR__ . '/../';
    $opt['rootpath'] = $rootpath;
}

// yepp, we will use UTF-8
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_language('uni');

//load default webserver-settings and common includes
require_once __DIR__ . '/consts.inc.php';
require_once __DIR__ . '/settings.inc.php';
require_once __DIR__ . '/../lib2/errorhandler.inc.php';

// check for banned UAs
$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
foreach ($opt['page']['banned_user_agents'] as $ua) {
    if (strpos($useragent, $ua) !== false) {
        die();
    }
}

// basic PHP settings
date_default_timezone_set($timezone);
register_errorhandlers();

if (isset($debug_page) && $debug_page) {
    ini_set('display_errors', true);
    ini_set('error_reporting', E_ALL);
} else {
    ini_set('display_errors', false);
    ini_set('error_reporting', E_ALL & ~E_NOTICE);
}

$dblink_slave = false;
$db_error = 0;

// prepare EMail-From
$emailheaders = 'From: "' . $emailaddr . '" <' . $emailaddr . '>';

/**
 * @param string $module
 * @param int $eventId
 * @param $userId
 * @param $objectid1
 * @param int $objectid2
 * @param string $logtext
 * @param $details
 */
function logentry($module, $eventId, $userId, $objectid1, $objectid2, $logtext, $details): void
{
    sql(
        "INSERT INTO logentries (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`, `details`)
         VALUES ('&1', '&2', '&3', '&4', '&5', '&6', '&7')",
        $module,
        $eventId,
        $userId,
        $objectid1,
        $objectid2,
        $logtext,
        serialize($details)
    );
}

// read a file and return it as a string
// WARNING: no huge files!
function read_file($file = '')
{
    $content = false;
    $fh = fopen($file, 'r');
    if ($fh) {
        $content = fread($fh, filesize($file));
    }

    fclose($fh);

    return $content;
}

function escape_javascript($text)
{
    return str_replace('\'', '\\\'', str_replace('"', '&quot;', $text));
}

// called if mysqli_query failed, sends email to sysadmin
/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 */
function sql_failed(): void
{
    sql_error();
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param string $sql
 * @param mixed $default
 */
function sqlValue($sql, $default)
{
    $rs = sql($sql);
    if ($r = sql_fetch_row($rs)) {
        if ($r[0] == null) {
            return $default;
        }

        return $r[0];
    }

    return $default;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $sql
 * @param $default
 * @return mixed
 */
function sql_value_slave($sql, $default)
{
    $rs = sql_slave($sql);
    if ($r = sql_fetch_row($rs)) {
        if ($r[0] == null) {
            return $default;
        }

        return $r[0];
    }

    return $default;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param string $name
 * @param string $default
 * @return string
 */
function getSysConfig($name, $default)
{
    return sqlValue('SELECT `value` FROM `sysconfig` WHERE `name`=\'' . sql_escape($name) . '\'', $default);
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param string $name
 * @param string $value
 */
function setSysConfig($name, $value): void
{
    if (sqlValue('SELECT COUNT(*) FROM sysconfig WHERE name=\'' . sql_escape($name) . '\'', 0) == 1) {
        sql(
            "UPDATE `sysconfig` SET `value`='&1' WHERE `name`='&2' LIMIT 1",
            $value,
            $name
        );
    } else {
        sql(
            "INSERT INTO `sysconfig` (`name`, `value`) VALUES ('&1', '&2')",
            $name,
            $value
        );
    }
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $sql
 * @return mysqli_result
 */
function sql($sql)
{
    global $dblink;

    // prepare args
    $args = func_get_args();
    unset($args[0]);

    if (isset($args[1]) && is_array($args[1])) {
        $tmp_args = $args[1];
        unset($args);

        // correct indizes
        $args = array_merge([0], $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    return sql_internal($dblink, $sql, false, $args);
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $sql
 * @return mysqli_result
 */
function sql_slave($sql)
{
    throw new InvalidArgumentException('sql slave support was removed!');
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $_dblink
 * @param $sql
 * @return mysqli_result
 */
function sql_internal($_dblink, $sql)
{
    global $sql_warntime;
    global $sql_replacements;

    $args = func_get_args();
    unset($args[0], $args[1], $args[2]);

    /* as an option, you can give as second parameter an array
     * with all values for the placeholder. The array has to be
     * with numeric indices.
     */
    if (isset($args[3]) && is_array($args[3])) {
        $tmp_args = $args[3];
        unset($args);

        // correct indices
        $args = array_merge([0], $tmp_args);
        unset($tmp_args);
        unset($args[0]);
    }

    $sqlpos = 0;
    $filtered_sql = '';

    // $sql von vorne bis hinten durchlaufen und alle &x ersetzen
    $nextarg = mb_strpos($sql, '&');
    while ($nextarg !== false) {
        // muss dieses & ersetzt werden, oder ist es escaped?
        $escapesCount = 0;
        while ((($nextarg - $escapesCount - 1) > 0) && (mb_substr($sql, $nextarg - $escapesCount - 1, 1) == '\\')) {
            $escapesCount++;
        }
        if (($escapesCount % 2) == 1) {
            $nextarg++;
        } else {
            $nextchar = mb_substr($sql, $nextarg + 1, 1);
            if (is_numeric($nextchar)) {
                $arglength = 0;
                $arg = '';

                // nächstes Zeichen das keine Zahl ist herausfinden
                while (mb_ereg_match('^[0-9]{1}', $nextchar) == 1) {
                    $arg .= $nextchar;

                    $arglength++;
                    $nextchar = mb_substr($sql, $nextarg + $arglength + 1, 1);
                }

                // ok ... ersetzen
                $filtered_sql .= mb_substr($sql, $sqlpos, $nextarg - $sqlpos);
                $sqlpos = $nextarg + $arglength;

                if (isset($args[$arg])) {
                    if (is_numeric($args[$arg])) {
                        $filtered_sql .= $args[$arg];
                    } else {
                        if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '\'') &&
                            (mb_substr($sql, $sqlpos + 1, 1) == '\'')
                        ) {
                            $filtered_sql .= sql_escape($args[$arg]);
                        } elseif ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '`') &&
                            (mb_substr($sql, $sqlpos + 1, 1) == '`')
                        ) {
                            $filtered_sql .= sql_escape($args[$arg]);
                        } else {
                            sql_error();
                        }
                    }
                } else {
                    // NULL
                    if ((mb_substr($sql, $sqlpos - $arglength - 1, 1) == '\'') &&
                        (mb_substr($sql, $sqlpos + 1, 1) == '\'')
                    ) {
                        // Anführungszeichen weg machen und NULL einsetzen
                        $filtered_sql = mb_substr($filtered_sql, 0, mb_strlen($filtered_sql) - 1);
                        $filtered_sql .= 'NULL';
                        $sqlpos++;
                    } else {
                        $filtered_sql .= 'NULL';
                    }
                }

                $sqlpos++;
            } else {
                $arglength = 0;
                $arg = '';

                // nächstes Zeichen das kein Buchstabe/Zahl ist herausfinden
                while (mb_ereg_match('^[a-zA-Z0-9]{1}', $nextchar) == 1) {
                    $arg .= $nextchar;

                    $arglength++;
                    $nextchar = mb_substr($sql, $nextarg + $arglength + 1, 1);
                }

                // ok ... ersetzen
                $filtered_sql .= mb_substr($sql, $sqlpos, $nextarg - $sqlpos);

                if (isset($sql_replacements[$arg])) {
                    $filtered_sql .= $sql_replacements[$arg];
                } else {
                    sql_error();
                }

                $sqlpos = $nextarg + $arglength + 1;
            }
        }

        $nextarg = mb_strpos($sql, '&', $nextarg + 1);
    }

    // rest anhängen
    $filtered_sql .= mb_substr($sql, $sqlpos);

    // \& durch & ersetzen
    $nextarg = mb_strpos($filtered_sql, '\&');
    while ($nextarg !== false) {
        $escapesCount = 0;
        while ((($nextarg - $escapesCount - 1) > 0) &&
            (mb_substr($filtered_sql, $nextarg - $escapesCount - 1, 1) == '\\')) {
            $escapesCount++;
        }
        if (($escapesCount % 2) == 0) {
            // \& ersetzen durch &
            $filtered_sql = mb_substr($filtered_sql, 0, $nextarg) . '&' . mb_substr($filtered_sql, $nextarg + 2);
            $nextarg--;
        }

        $nextarg = mb_strpos($filtered_sql, '\&', $nextarg + 2);
    }

    //
    // ok ... hier ist filtered_sql fertig
    //

    /* todo:
        - errorlogging
        - LIMIT
        - DROP/DELETE ggf. blocken
    */

    // Zeitmessung für die Ausführung
    $cSqlExecution = new CBench;
    $cSqlExecution->start();

    $result = mysqli_query($_dblink, $filtered_sql);
    if ($result === false) {
        sql_error();
    }

    $cSqlExecution->stop();

    if ($sql_warntime > 0 && $cSqlExecution->diff() > $sql_warntime) {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? "\r\n" . $_SERVER['HTTP_USER_AGENT'] : '';
        sql_warn('execution took ' . $cSqlExecution->diff() . ' seconds' . $ua);
    }

    return $result;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $value
 * @return false|string
 */
function sql_escape($value)
{
    global $dblink;
    $value = mysqli_real_escape_string($dblink, $value);
    $value = mb_ereg_replace('&', '\&', $value);

    return $value;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $value
 * @return false|mixed|string
 */
function sql_escape_backtick($value)
{
    $value = sql_escape($value);
    $value = str_replace('`', '``', $value);

    return $value;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 */
function sql_error(): void
{
    global $debug_page;
    global $sql_errormail;
    global $emailheaders;
    global $absolute_server_URI;
    global $interface_output;
    global $dberrormsg;
    global $db_error;
    global $dblink;

    $db_error += 1;
    $msql_error = mysqli_connect_error() . ': ' . mysqli_error($dblink);
    if ($db_error > 1) {
        $msql_error .= "\n(** error recursion **)";
    }

    if ($sql_errormail != '') {
        // sendout email
        $email_content = $msql_error;
        $email_content .= "\n--------------------\n";
        $email_content .= print_r(debug_backtrace(), true);
        if (admin_errormail($sql_errormail, 'sql_error', $email_content, $emailheaders)) {
            mb_send_mail($sql_errormail, 'sql_error: ' . $absolute_server_URI, $email_content, $emailheaders);
        }
    }

    if ($interface_output == 'html') {
        // display errorpage
        $errmsg = $dberrormsg . ($debug_page ? '<br />' . $msql_error : '');
        if ($db_error <= 1) {
            tpl_errorMsg('sql_error', $errmsg);
        } else {
            // database error recursion, because another error occurred while trying to
            // build the error template (e.g. because connection was lost, or an error mail
            // could not load translations from database)

            $errtitle = 'Datenbankfehler';
            require __DIR__ . '/../html/error.php';
        }
        exit;
    } elseif ($interface_output == 'plain') {
        echo "\n";
        echo 'sql_error' . "\n";
        if ($debug_page) {
            echo $msql_error . "\n";
        }
        echo '---------' . "\n";
        echo print_r(debug_backtrace(), true) . "\n";
        exit;
    }

    die('sql_error');
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param string $warnmessage
 */
function sql_warn($warnmessage): void
{
    global $sql_errormail;
    global $emailheaders;
    global $absolute_server_URI;

    $email_content = $warnmessage;
    $email_content .= "\n--------------------\n";
    $email_content .= print_r(debug_backtrace(), true);

    if (admin_errormail($sql_errormail, 'sql_warn', $email_content, $emailheaders)) {
        @mb_send_mail($sql_errormail, 'sql_warn: ' . $absolute_server_URI, $email_content, $emailheaders);
    }
}

/*
    Ersatz für die in Mysql eingebauten Funktionen
*/
/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param resource $rs
 * @return array
 */
function sql_fetch_array($rs)
{
    return mysqli_fetch_array($rs);
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $rs
 * @return array
 */
function sql_fetch_assoc($rs)
{
    return mysqli_fetch_assoc($rs);
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $rs
 * @return array
 */
function sql_fetch_row($rs)
{
    return mysqli_fetch_row($rs);
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param resource $rs
 * @return array|null
 */
function sql_fetch_column($rs)
{
    $col = [];
    while ($r = sql_fetch_row($rs)) {
        if (count($r) != 1) {
            return null;
        }
        $col[] = $r[0];
    }
    sql_free_result($rs);

    return $col;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * @param $rs
 * @return bool
 */
function sql_free_result($rs)
{
    return mysqli_free_result($rs);
}

function mb_trim($str)
{
    $bLoop = true;
    while ($bLoop == true) {
        $sPos = mb_substr($str, 0, 1);

        if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0") {
            $str = mb_substr($str, 1, mb_strlen($str) - 1);
        } else {
            $bLoop = false;
        }
    }

    $bLoop = true;
    while ($bLoop == true) {
        $sPos = mb_substr($str, -1, 1);

        if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0") {
            $str = mb_substr($str, 0, mb_strlen($str) - 1);
        } else {
            $bLoop = false;
        }
    }

    return $str;
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * disconnect the database
 */
function db_disconnect(): void
{
    global $dbpconnect, $dblink;

    //is connected and no persistent connect used?
    if (($dbpconnect == false) && ($dblink !== false)) {
        @mysqli_close($dblink);
        $dblink = false;
    }
}

/**
 * @deprecated use DBAL Conenction instead. See adminreports.php for an example implementation
 * database handling
 */
function db_connect(): void
{
    global $dblink, $dbusername, $dbname, $dbserver, $dbpasswd;

    //connect to the database by the given method - no php error reporting!
    $dblink = mysqli_connect($dbserver, $dbusername, $dbpasswd, $dbname);

    if (!$dblink instanceof mysqli) {
        throw new InvalidArgumentException('cannot connect to database');
    }
}

function get_site_urls($domain)
{
    global $opt;

    if (!$domain) {
        $domain = parse_url($opt['page']['default_primary_url'], PHP_URL_HOST);
    }
    if ($domain == parse_url($opt['page']['default_primary_url'], PHP_URL_HOST) ||
        !isset($opt['domain'][$domain]['url'])
    ) {
        $site_url = $opt['page']['default_primary_url'];
        $shortlink_url = $opt['page']['default_primary_shortlink_url'];
    } else {
        $protocol = 'http';
        if (isset($opt['domain'][$domain]['https']['is_default']) && $opt['domain'][$domain]['https']['is_default']) {
            $protocol = 'https';
        }

        $site_url = $protocol . strstr($opt['domain'][$domain]['url'], '://');
        if (isset($opt['domain'][$domain]['shortlink_domain']) && $opt['domain'][$domain]['shortlink_domain']) {
            $shortlink_url = $protocol . '://' . $opt['domain'][$domain]['shortlink_domain'] . '/';
        } else {
            $shortlink_url = false;
        }
    }

    return [
        'site_url' => $site_url,
        'shortlink_url' => $shortlink_url,
    ];
}

/**
 * @param string $filename
 * @param mixed $language
 * @param mixed $domain
 * @return bool|string
 */
function fetch_email_template($filename, $language, $domain)
{
    global $opt, $rootpath;

    if (!$language) {
        $language = $opt['template']['default']['locale'];
    }
    $language = strtolower($language);
    if (!file_exists(__DIR__ . '/../lang/de/ocstyle/email/' . $language . '/' . $filename . '.email')) {
        $language = 'en';
    }
    $mailtext = read_file(__DIR__ . '/../lang/de/ocstyle/email/' . $language . '/' . $filename . '.email');

    $urls = get_site_urls($domain);
    $mailtext = mb_ereg_replace('{site_url}', $urls['site_url'], $mailtext);
    if ($urls['shortlink_url']) {
        $mailtext = mb_ereg_replace('{shortlink_url}', $urls['shortlink_url'], $mailtext);
    } else {
        $mailtext = mb_ereg_replace('{shortlink_url}', $urls['site_url'], $mailtext);
    }

    $mailtext = mb_ereg_replace('{email_contact}', $opt['mail']['contact'], $mailtext);

    return $mailtext;
}
