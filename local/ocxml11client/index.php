#!/usr/local/bin/php -q
<?php
/***************************************************************************
 * ./util/ocxml11client/index.php
 * -------------------
 * begin                : Sun January 14 2005
 *
 * For license information see doc/license.txt
 *
 * This code is of 2007 and has to be verified to be compatible with
 * current OC Server Version 3.
 ***************************************************************************/

/***************************************************************************
 *
 * Unicode Reminder メモ
 *
 * ocxml11-Interface-Client
 * Nicht für Produktivsystem!
 *
 * Die Dokuemntation beachten:
 * http://www.opencaching.de/doc/xml/xml11.htm
 *
 * TODO:
 * - removed_objects ... Abhängigkeiten prüfen
 * - nicht importierte records merken
 * - Test mit aktuellem OC.de-Stand
 ***************************************************************************/

$rootpath = __DIR__ . '/../../htdocs/';

// chdir to proper directory (needed for cronjobs)
chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

require_once $rootpath . 'lib/clicompatbase.inc.php';
require_once 'xml2array.inc.php';
require_once 'settings.php';

global $sql_warntime;
$sql_warntime = 3;

switchdb();
sql('SET @XMLSYNC=1');

cleartmpdir();
getxmlfiles();
unzipxmlfiles();
importxmlfiles();
analyzedb();

restorevalues();
downloadpictures();

function downloadpictures()
{
    global $opt;

    // Bilder abrufen?
    if ($opt['pictures']['download'] != 1) {
        return;
    }

    $rs = sql('SELECT COUNT(*) `c` FROM `pictures` WHERE `local`=0');
    $rCount = sql_fetch_array($rs);
    mysql_free_result($rs);

    $nFileNr = 0;
    $rs = sql('SELECT `id`, `url` FROM `pictures` WHERE `local`=0');
    while ($r = sql_fetch_array($rs)) {
        $nFileNr ++;
        $fileparts = mb_split('/', $r['url']);
        $filename = $fileparts[count($fileparts) - 1];

        echo 'Downloading file ' . $nFileNr . ' of ' . $rCount['c'] . ': ' . $filename . "\n";

        $success = true;
        if (!file_exists($opt['pictures']['directory'] . $filename)) {
            if (!@copy($r['url'], $opt['pictures']['directory'] . $filename)) {
                echo 'error: Download nicht erfolgreich' . "\n";
                $success = false;
            }
        }

        if ($success == true) {
            sql(
                "UPDATE `pictures` SET `local`=1, `url`='&1' WHERE `id`='&2'",
                $opt['pictures']['url'] . $filename,
                $r['id']
            );
        }
    }
    mysql_free_result($rs);

}

function resetdb()
{
    // alle datentabellen leeren
    $datatables[] = 'cache_desc';
    $datatables[] = 'cache_logs';
    $datatables[] = 'cache_visits';
    $datatables[] = 'cachelist_user';
    $datatables[] = 'cachelists';
    $datatables[] = 'cachelists_caches';
    $datatables[] = 'cachelists_waiting';
    $datatables[] = 'caches';
    $datatables[] = 'caches_attributes';
    $datatables[] = 'desc_search';
    $datatables[] = 'email_user';
    $datatables[] = 'logins';
    $datatables[] = 'pictures';
    $datatables[] = 'removed_objects';
    $datatables[] = 'queries';
    $datatables[] = 'user';
    $datatables[] = 'watches_notified';
    $datatables[] = 'watches_waiting';
    $datatables[] = 'xmlsession_data';

    setSysConfig('ocxml11client_lastupdate', '2005-08-01 00:00:00');
    sql('UPDATE `xmlsession` SET `cleaned`=1');

    foreach ($datatables as $database) {
        sql('TRUNCATE TABLE `&1`', $database);
    }
}

function analyzedb()
{
    // alle tabellen analysieren
    $rs = sql('SHOW TABLES');
    while ($r = sql_fetch_array($rs)) {
        sql('ANALYZE TABLE `&1`', $r[0]);
    }
    mysql_free_result($rs);
}

function optimizedb()
{
    // alle tabellen optimieren
    $rs = sql('SHOW TABLES');
    while ($r = sql_fetch_array($rs)) {
        sql('OPTIMIZE TABLE `&1`', $r[0]);
    }
    mysql_free_result($rs);
}

function getxmlfiles()
{
    global $opt;

    // letztes Update ermitteln
    $lastUpdate = getSysConfig('ocxml11client_lastupdate', '2005-08-01 00:00:00');

    // URL zusammenbauen
    $url = $opt['url'];
    $url = mb_ereg_replace('{modifiedsince}', date('YmdHis', strtotime($lastUpdate) - 60), $url);
    $url = mb_ereg_replace('{user}', $opt['sync']['user'], $url);
    $url = mb_ereg_replace('{cache}', $opt['sync']['cache'], $url);
    $url = mb_ereg_replace('{cachedesc}', $opt['sync']['cachedesc'], $url);
    $url = mb_ereg_replace('{cachelog}', $opt['sync']['cachelog'], $url);
    $url = mb_ereg_replace('{picture}', $opt['sync']['picture'], $url);
    $url = mb_ereg_replace('{picturefromcachelog}', $opt['sync']['picturefromcachelog'], $url);
    $url = mb_ereg_replace('{removedobject}', $opt['sync']['removedobject'], $url);
    $url = mb_ereg_replace('{session}', $opt['session'], $url);
    $url = mb_ereg_replace('{zip}', $opt['zip'], $url);

    if ($opt['bycountry'] == 1) {
        $url .= $opt['urlappend_country'];
        $url = mb_ereg_replace('{country}', $opt['country'], $url);
    } else {
        if ($opt['bycoords'] == 1) {
            $url .= $opt['urlappend_coords'];
            $url = mb_ereg_replace('{lon}', $opt['lon'], $url);
            $url = mb_ereg_replace('{lat}', $opt['lat'], $url);
            $url = mb_ereg_replace('{distance}', $opt['distance'], $url);
        }
    }

    if ($opt['zip'] == '0') {
        $fileext = '.xml';
    } else {
        if ($opt['zip'] == 'zip') {
            $fileext = '.xml.zip';
        } else {
            if ($opt['zip'] == 'gzip') {
                $fileext = '.xml.gz';
            } else {
                if ($opt['zip'] == 'bzip2') {
                    $fileext = '.xml.bz2';
                } else {
                    die('error: unkown zip method');
                }
            }
        }
    }

    if ($opt['session'] == 1) {
        // records abfragen
        $sessionfile = $opt['tmpdir'] . 'session.xml';
        if (file_exists($sessionfile)) {
            unlink($sessionfile);
        }
        copy($url, $sessionfile);

        $xmlParser = new xml2Array();
        $session = $xmlParser->parse(read_file($sessionfile));
        $xmlParser = null;
        unlink($sessionfile);

        $sessionid = $session['OCXMLSESSION']['SESSIONID']['DATA'];
        $recordscount = $session['OCXMLSESSION']['RECORDS']['USER'];
        $recordscount += $session['OCXMLSESSION']['RECORDS']['CACHE'];
        $recordscount += $session['OCXMLSESSION']['RECORDS']['CACHEDESC'];
        $recordscount += $session['OCXMLSESSION']['RECORDS']['CACHELOG'];
        $recordscount += $session['OCXMLSESSION']['RECORDS']['PICTURE'];
        $recordscount += $session['OCXMLSESSION']['RECORDS']['REMOVEOBJECT'];

        echo 'Abruf seit ' . $lastUpdate . "\n";
        echo "------------------------\n";
        echo 'Session-Id: ' . $sessionid . "\n";
        echo 'User: ' . $session['OCXMLSESSION']['RECORDS']['USER'] . "\n";
        echo 'Cache: ' . $session['OCXMLSESSION']['RECORDS']['CACHE'] . "\n";
        echo 'Cachdesc: ' . $session['OCXMLSESSION']['RECORDS']['CACHEDESC'] . "\n";
        echo 'Cachelog: ' . $session['OCXMLSESSION']['RECORDS']['CACHELOG'] . "\n";
        echo 'Picture: ' . $session['OCXMLSESSION']['RECORDS']['PICTURE'] . "\n";
        echo 'Removedobject: ' . $session['OCXMLSESSION']['RECORDS']['REMOVEOBJECT'] . "\n";
        echo "------------------------\n";
        echo 'Summe: ' . $recordscount . "\n";

        $filescount = ($recordscount + (500 - $recordscount % 500)) / 500;

        echo 'Anzahl der Pakete: ' . $filescount . "\n";
        echo "\n";

        if ($recordscount == 0) {
            echo "No new data, exiting\n";
            exit;
        }

        for ($i = 1; $i <= $filescount; $i ++) {
            echo "Download Paket: " . $i . ' von ' . $filescount . "\n";

            $fileurl = $opt['url_getsession'];
            $fileurl = mb_ereg_replace('{sessionid}', $sessionid, $fileurl);
            $fileurl = mb_ereg_replace('{file}', $i, $fileurl);
            $fileurl = mb_ereg_replace('{zip}', $opt['zip'], $fileurl);
            $target = $opt['tmpdir'] . $sessionid . '-' . sprintf('%04d', $i) . $fileext;

            copy($fileurl, $target);
        }
    } else {
        echo 'Download ...' . "\n";
        $target = $opt['tmpdir'] . date('YmdHis') . $fileext;
        copy($url, $target);
    }
}

function unzipxmlfiles()
{
    global $opt;

    // alle zips entpacken
    $hDir = opendir($opt['tmpdir']);
    while (false !== ($file = readdir($hDir))) {
        if (is_file($opt['tmpdir'] . $file)) {
            $bCopy = false;
            if (mb_substr($file, mb_strrpos($file, '.')) == '.zip') {
                echo 'Unzipping file ' . $file . "\n";
                system(
                    $opt['unzip'] . ' --type=zip --src="' . $opt['rel_tmpdir'] . '/' . $file . '" --dst="' . $opt['rel_tmpdir'] . '"'
                );
            } else {
                if (mb_substr($file, mb_strrpos($file, '.')) == '.gz') {
                    echo 'Unzipping file ' . $file . "\n";
                    system(
                        $opt['unzip'] . ' --type=gzip --src="' . $opt['rel_tmpdir'] . '/' . $file . '" --dst="' . $opt['rel_tmpdir'] . '"'
                    );
                } else {
                    if (mb_substr($file, mb_strrpos($file, '.')) == '.bz2') {
                        echo 'Unzipping file ' . $file . "\n";
                        system(
                            $opt['unzip'] . ' --type=bzip2 --src="' . $opt['rel_tmpdir'] . '/' . $file . '" --dst="' . $opt['rel_tmpdir'] . '"'
                        );
                    } else {
                        if (mb_substr($file, mb_strrpos($file, '.')) == '.xml') {
                            $bCopy = true;
                        }
                    }
                }
            }

            // und jetzt die gezippte Datei verschieben
            $archivdir = $opt['archivdir'];
            $archivdir .= $opt['curdb'] . '/';

            if (!is_dir($archivdir)) {
                mkdir($archivdir);
            }

            if ($bCopy == true) {
                copy($opt['tmpdir'] . $file, $archivdir . '/' . $file);
            } else {
                rename($opt['tmpdir'] . $file, $archivdir . '/' . $file);
            }
        }
    }
    closedir($hDir);
}

function importxmlfiles()
{
    global $opt;

    $files = [];

    $hDir = opendir($opt['tmpdir']);
    while (false !== ($file = readdir($hDir))) {
        if (is_file($opt['tmpdir'] . $file)) {
            if ($file != '.cvsignore') {
                $files[] = $opt['tmpdir'] . $file;
            }
        }
    }
    closedir($hDir);

    sort($files);
    foreach ($files as $file) {
        importxmlfile($file);
    }
}

function importxmlfile($file)
{
    echo 'Importing file ' . $file . "\n";

    $xmlReader = new xmlReader();
    $xmlReader->open($file);
    $xmlReader->read();
    if ($xmlReader->nodeType != XMLReader::DOC_TYPE) {
        echo 'error: DOCTYPE expected, aborted';

        return false;
    }
    if ($xmlReader->name != 'oc11xml') {
        echo 'error: wrong DOCTYPE, aborted';

        return false;
    }

    $xmlReader->read();
    if ($xmlReader->nodeType != XMLReader::ELEMENT) {
        echo 'error: First element expected, aborted';

        return false;
    }
    if ($xmlReader->name != 'oc11xml') {
        echo 'error: first element not valid, aborted';

        return false;
    }
    $xmlReader->moveToFirstAttribute();
    while (($xmlReader->name != 'date') && $xmlReader->moveToNextAttribute()) {
        ;
    }

    if ($xmlReader->name == 'date') {
        $starttime = strtotime($xmlReader->value);
    } else {
        $starttime = strtotime('2005-08-01 00:00:00');
    }

    // ok ... machen wir mal ...
    $xmlReader->read();
    do {
        if ($xmlReader->nodeType == XMLReader::ELEMENT) {
            $elementName = mb_strtoupper($xmlReader->name);

            /*
                ... diese Node in Array umwandeln

            $node['USER']['__DATA'] = '';
            $node['USER']['ID']['__ATTR']['ID'] = '101016';
            $node['USER']['ID']['__DATA'] = 'B97CE517-4D74-B6CE-BED8-EF76662FB7EE';
            $node['USER']['USERNAME']['__DATA'] = 'Team BMW-Biker';
            $node['USER']['PMR']['__DATA'] = '0';
            $node['USER']['DATECREATED']['__DATA'] = '2005-08-14 00:00:00';
            $node['USER']['LASTMODIFIED']['__DATA'] = '2005-10-30 19:26:51';
            */
            unset($node);
            $node[$elementName]['__DATA'] = '';

            $sSubElement = '';
            $nRecursionLevel = 1;

            while ($xmlReader->read() && ($nRecursionLevel > 0)) {
                if ($xmlReader->nodeType == XMLReader::TEXT) {
                    if ($sSubElement != '') {
                        $node[$elementName][$sSubElement]['__DATA'] = $xmlReader->value;
                    } else {
                        $node[$elementName]['__DATA'] = $xmlReader->value;
                    }
                } else {
                    if ($xmlReader->nodeType == XMLReader::CDATA) {
                        if ($sSubElement != '') {
                            $node[$elementName][$sSubElement]['__DATA'] = $xmlReader->value;
                        } else {
                            $node[$elementName]['__DATA'] = $xmlReader->value;
                        }
                    } else {
                        if ($xmlReader->nodeType == XMLReader::ELEMENT) {
                            if ($sSubElement != '') {
                                // vorheriges element zuerst schlißen
                                $sSubElement = '';
                                $nRecursionLevel --;
                            }

                            $sSubElement = mb_strtoupper($xmlReader->name);
                            $nRecursionLevel ++;

                            // attribute auslesen
                            if ($xmlReader->moveToFirstAttribute()) {
                                $node[$elementName][$sSubElement]['__ATTR'][mb_strtoupper(
                                    $xmlReader->name
                                )] = $xmlReader->value;

                                while ($xmlReader->moveToNextAttribute()) {
                                    $node[$elementName][$sSubElement]['__ATTR'][mb_strtoupper(
                                        $xmlReader->name
                                    )] = $xmlReader->value;
                                }
                            }
                        } else {
                            if ($xmlReader->nodeType == XMLReader::END_ELEMENT) {
                                $sSubElement = '';
                                $nRecursionLevel --;
                            }
                        }
                    }
                }
            }

            switch ($elementName) {
                case 'USER':
                    ImportUserArray($node['USER']);
                    break;
                case 'CACHE':
                    ImportCacheArray($node['CACHE']);
                    break;
                case 'CACHEDESC':
                    ImportCacheDescArray($node['CACHEDESC']);
                    break;
                case 'CACHELOG':
                    ImportCachelogArray($node['CACHELOG']);
                    break;
                case 'PICTURE':
                    ImportPictureArray($node['PICTURE']);
                    break;
                case 'REMOVEDOBJECT':
                    ImportRemovedObjectArray($node['REMOVEDOBJECT']);
                    break;
                default:
                    echo 'Unknown Element "' . $xmlReader->name . '", skipping' . "\n";
                    break;
            }
        }
    } while ($xmlReader->read());

    $xmlReader->close();
    $xmlReader = null;

    // zeitstempel notieren
    $oldstarttime = strtotime(getSysConfig('ocxml11client_lastupdate', '2005-08-01 00:00:00'));
    if ($oldstarttime < $starttime) {
        setSysConfig('ocxml11client_lastupdate', date('Y-m-d H:i:s', $starttime));
    }
}

function cleartmpdir()
{
    global $opt;

    $hDir = opendir($opt['tmpdir']);
    while (false !== ($file = readdir($hDir))) {
        if (is_file($opt['tmpdir'] . $file)) {
            if ($file != '.cvsignore') {
                unlink($opt['tmpdir'] . $file);
            }
        }
    }
    closedir($hDir);
}

function switchdb()
{
    global $argv, $opt, $dblink;
    global $dbname, $dbserver, $dbusername, $dbpasswd;

    $opt['curdb'] = 0;

    foreach ($argv as $arg) {
        // andere DB connecten?
        if (mb_substr($arg, 0, 5) == '--db=') {
            if (!is_numeric(mb_substr($arg, 5))) {
                die('invalid alternative DB' . "\n");
            }

            $nDb = mb_substr($arg, 5);

            if ($nDb != 0) {
                if (!isset($opt['db'][$nDb])) {
                    die('invalid alternative DB' . "\n");
                }

                mysql_close($dblink);
                $dblink = mysql_connect($opt['db'][1]['server'], $opt['db'][1]['username'], $opt['db'][1]['passwd']);
                if ($dblink !== false) {
                    sql("SET NAMES 'utf8'");
                    sql('USE `&1`', $opt['db'][1]['name']);
                } else {
                    die('Connect to alternative DB failed' . "\n");
                }

                $opt['curdb'] = $nDb;
            }
        }
    }

    if ($opt['curdb'] == 0) {
        $dblink = mysql_connect($dbserver, $dbusername, $dbpasswd);
        if ($dblink !== false) {
            sql("SET NAMES 'utf8'");
            sql('USE `&1`', $dbname);
        } else {
            die('Connect to alternative DB failed' . "\n");
        }
    }
}


function ImportUserArray($r)
{
    // prüfen ob alle elemente vorhanden sind
    if (!isset($r['ID']['__DATA']) ||
        !isset($r['USERNAME']['__DATA']) ||
        !isset($r['LASTMODIFIED']['__DATA'])
    ) {
        echo 'warn: ImportUserArray required element not defined' . "\n";

        return;
    }

    if (!isset($r['DATECREATED']['__DATA'])) {
        $r['DATECREATED']['__DATA'] = date('Y-m-d H:i:s');
    }

    if (!isset($r['PMR']['__DATA'])) {
        $r['PMR']['__DATA'] = 0;
    }

    if (removedObject($r['ID']['__DATA'])) {
        return;
    }

    // abfragen, ob user schon existiert
    $rs = sql("SELECT `user_id`, `last_modified` FROM `user` WHERE `uuid`='&1'", $r['ID']['__DATA']);
    if ($rid = sql_fetch_array($rs)) {
        // update
        if (strtotime($rid['last_modified']) < strtotime($r['LASTMODIFIED']['__DATA'])) {
            // existiert username schon?
            $rsUser = sql("SELECT `user_id` FROM `user` WHERE `username`='&1'", $r['USERNAME']['__DATA']);
            if ($rUser = sql_fetch_array($rsUser)) {
                if ($rUser['user_id'] != $rid['user_id']) {
                    importError('user', $r['ID']['__DATA'], $r, 'new username already exists, skipping');

                    return;
                }
            }
            mysql_free_result($rsUser);

            sql(
                "UPDATE `user` SET `last_modified`='&1', `username`='&2', `pmr_flag`=&3 WHERE `user_id`=&4",
                $r['LASTMODIFIED']['__DATA'],
                $r['USERNAME']['__DATA'],
                ($r['PMR']['__DATA'] == '0' ? '0' : '1'),
                $rid['user_id']
            );
        }
    } else {
        // existiert username schon?
        $rsUser = sql("SELECT COUNT(*) `c` FROM `user` WHERE `username`='&1'", $r['USERNAME']['__DATA']);
        $rUser = sql_fetch_array($rsUser);
        mysql_free_result($rsUser);

        if ($rUser['c'] > 0) {
            importError('user', $r['ID']['__DATA'], $r, 'username already exists, skipping');

            return;
        }

        // neu anlegen
        sql(
            "INSERT INTO `user` (`username`,
                               `last_modified`,
                               `country`,
                               `date_created`,
                               `uuid`,
                               `pmr_flag`)
                       VALUES ('&1', '&2', 'XX', '&3', '&4', &5)",
            $r['USERNAME']['__DATA'],
            $r['LASTMODIFIED']['__DATA'],
            $r['DATECREATED']['__DATA'],
            $r['ID']['__DATA'],
            ($r['PMR']['__DATA'] == '0' ? '0' : '1')
        );
    }
    mysql_free_result($rs);
}

function ImportCacheArray($r)
{
    // prüfen ob alle elemente vorhanden sind
    if (!isset($r['ID']['__DATA']) ||
        !isset($r['USERID']['__ATTR']['UUID']) ||
        !isset($r['NAME']['__DATA']) ||
        !isset($r['LONGITUDE']['__DATA']) ||
        !isset($r['LATITUDE']['__DATA']) ||
        !isset($r['TYPE']['__ATTR']['ID']) ||
        !isset($r['STATUS']['__ATTR']['ID']) ||
        !isset($r['COUNTRY']['__ATTR']['ID']) ||
        !isset($r['SIZE']['__ATTR']['ID']) ||
        !isset($r['DIFFICULTY']['__DATA']) ||
        !isset($r['TERRAIN']['__DATA']) ||
        !isset($r['RATING']['__ATTR']['WAYLENGTH']) ||
        !isset($r['RATING']['__ATTR']['NEEDTIME']) ||
        !isset($r['WAYPOINTS']['__ATTR']['OC']) ||
        !isset($r['DATEHIDDEN']['__DATA']) ||
        !isset($r['DATECREATED']['__DATA']) ||
        !isset($r['LASTMODIFIED']['__DATA'])
    ) {
        echo 'warn: ImportCacheArray required element not defined' . "\n";

        return;
    }

    if (!isset($r['WAYPOINTS']['__ATTR']['GCCOM'])) {
        $r['WAYPOINTS']['__ATTR']['GCCOM'] = '';
    }

    if (!isset($r['WAYPOINTS']['__ATTR']['NCCOM'])) {
        $r['WAYPOINTS']['__ATTR']['NCCOM'] = '';
    }

    if ($r['RATING']['__ATTR']['NEEDTIME'] == '') {
        $r['RATING']['__ATTR']['NEEDTIME'] = 0;
    }

    if ($r['RATING']['__ATTR']['WAYLENGTH'] == '') {
        $r['RATING']['__ATTR']['WAYLENGTH'] = 0;
    }

    if (removedObject($r['ID']['__DATA'])) {
        return;
    }

    // prüfen, ob cache schon vorhanden
    $rs = sql(
        "SELECT `cache_id`, `user_id`, `last_modified`, `wp_oc`, `uuid` FROM `caches` WHERE `uuid`='&1'",
        $r['ID']['__DATA']
    );
    if ($rc = sql_fetch_array($rs)) {
        if (strtotime($rc['last_modified']) < strtotime($r['LASTMODIFIED']['__DATA'])) {
            // user unterschiedlich?
            $rsUser = sql("SELECT `user_id` FROM `user` WHERE `uuid`='&1'", $r['USERID']['__ATTR']['UUID']);
            if ($rUser = sql_fetch_array($rsUser)) {
                if ($rc['user_id'] != $rUser['user_id']) {
                    importError(
                        'cache',
                        $r['ID']['__DATA'],
                        $r,
                        'User has changed, not supported at the moment, skipping'
                    );

                    return;
                }
            } else {
                importError('cache', $r['ID']['__DATA'], $r, 'User does not exist');

                return;
            }
            mysql_free_result($rsUser);

            // waypoint unterschiedlich?
            $rsWaypoint = sql("SELECT `wp_oc` FROM `caches` WHERE `uuid`='&1'", $r['ID']['__DATA']);
            $rWaypoint = sql_fetch_array($rsWaypoint);
            if ($rWaypoint['wp_oc'] != $r['WAYPOINTS']['__ATTR']['OC']) {
                if ($rWaypoint['wp_oc'] != null) {
                    importError('cache', $r['ID']['__DATA'], $r, 'Waypoint does not match');

                    return;
                } else {
                    importWarn('cache', $r['ID']['__DATA'], $r, 'Waypoint does not match, i will set it');
                }
            }
            mysql_free_result($rsWaypoint);

            // update record
            sql(
                "UPDATE `caches` SET `name`='&1', `longitude`=&2, `latitude`=&3,
                                  `last_modified`='&4', `date_created`='&5', `type`=&6,
                                  `status`=&7, `country`='&8', `date_hidden`='&9',
                                  `size`=&10, `difficulty`=&11, `terrain`=&12,
                                  `search_time`=&13, `way_length`=&14, `wp_gc`='&15',
                                  `wp_oc`='&16' WHERE `uuid`='&17' LIMIT 1",
                $r['NAME']['__DATA'],
                $r['LONGITUDE']['__DATA'],
                $r['LATITUDE']['__DATA'],
                $r['LASTMODIFIED']['__DATA'],
                $r['DATECREATED']['__DATA'],
                $r['TYPE']['__ATTR']['ID'],
                $r['STATUS']['__ATTR']['ID'],
                $r['COUNTRY']['__ATTR']['ID'],
                $r['DATEHIDDEN']['__DATA'],
                $r['SIZE']['__ATTR']['ID'],
                $r['DIFFICULTY']['__DATA'],
                $r['TERRAIN']['__DATA'],
                $r['RATING']['__ATTR']['NEEDTIME'],
                $r['RATING']['__ATTR']['WAYLENGTH'],
                $r['WAYPOINTS']['__ATTR']['GCCOM'],
                $r['WAYPOINTS']['__ATTR']['OC'],
                $r['ID']['__DATA']
            );
        }
    } else {
        // userid ermitteln
        $rsUser = sql("SELECT `user_id` FROM `user` WHERE `uuid`='&1'", $r['USERID']['__ATTR']['UUID']);
        if (!($rUser = sql_fetch_array($rsUser))) {
            importError('cache', $r['ID']['__DATA'], $r, 'User does not exist, skipping');

            return;
        }
        mysql_free_result($rsUser);

        // waypoint prüfen
        $rsWp = sql("SELECT `wp_oc` FROM `caches` WHERE `wp_oc`='&1'", $r['WAYPOINTS']['__ATTR']['OC']);
        if (mysql_num_rows($rsWp) > 0) {
            importError('cache', $r['ID']['__DATA'], $r, 'Waypoint already exists, skipping');

            return;
        }
        sql_fetch_array($rsWp);

        // insert ...
        sql(
            "INSERT INTO caches (`user_id`, `name`, `longitude`,
                               `latitude`, `last_modified`, `date_created`,
                               `type`, `status`, `country`,
                               `date_hidden`,
                               `size`, `difficulty`,
                               `terrain`, `uuid`, `search_time`,
                               `way_length`, `wp_gc`,
                               `wp_oc`)
                       VALUES (  &1 ,  '&2',   &3 ,
                                 &4 ,  '&5',  '&6',
                                 &7 ,   &8 ,  '&9',
                               '&10',
                                &11 ,  &12 ,
                                &13 , '&14',  &15 ,
                                &16 , '&17',
                               '&18')",
            $rUser['user_id'],
            $r['NAME']['__DATA'],
            $r['LONGITUDE']['__DATA'],
            $r['LATITUDE']['__DATA'],
            $r['LASTMODIFIED']['__DATA'],
            $r['DATECREATED']['__DATA'],
            $r['TYPE']['__ATTR']['ID'],
            $r['STATUS']['__ATTR']['ID'],
            $r['COUNTRY']['__ATTR']['ID'],
            $r['DATEHIDDEN']['__DATA'],
            $r['SIZE']['__ATTR']['ID'],
            $r['DIFFICULTY']['__DATA'] * 2,
            $r['TERRAIN']['__DATA'] * 2,
            $r['ID']['__DATA'],
            $r['RATING']['__ATTR']['NEEDTIME'],
            $r['RATING']['__ATTR']['WAYLENGTH'],
            $r['WAYPOINTS']['__ATTR']['GCCOM'],
            $r['WAYPOINTS']['__ATTR']['OC']
        );
    }
    mysql_free_result($rs);
}

function ImportCacheDescArray($r)
{
    /*
        [ID][__DATA] => 7A894AEA-59EE-673B-C56B-6BC36E12701B
        [CACHEID][__DATA] => 4721CD92-824D-B8AF-C1C4-FA565E8C5D27
        [LANGUAGE][__ATTR][ID] => EN
        [SHORTDESC][__DATA] => Drive-by micro cache with a scenic view.
        [DESC][__ATTR][HTML] => 0
        [DESC][__DATA] => This micro cache leads you.
        [LASTMODIFIED][__DATA] => 2005-08-22 14:03:33
    */
    // prüfen ob alle elemente vorhanden sind
    if (!isset($r['ID']['__DATA']) ||
        !isset($r['CACHEID']['__DATA']) ||
        !isset($r['LANGUAGE']['__ATTR']['ID']) ||
        !isset($r['LASTMODIFIED']['__DATA'])
    ) {
        echo 'error: ImportCacheDescArray required element not defined' . "\n";

        return;
    }

    if (!isset($r['DESC']['__ATTR']['HTML'])) {
        $r['DESC']['__ATTR']['HTML'] = 0;
    }

    if (!isset($r['DESC']['__DATA'])) {
        $r['DESC']['__DATA'] = '';
    }

    if (!isset($r['SHORTDESC']['__DATA'])) {
        $r['SHORTDESC']['__DATA'] = '';
    }

    if (!isset($r['HINT']['__DATA'])) {
        $r['HINT']['__DATA'] = '';
    }

    if ($r['DESC']['__ATTR']['HTML'] != 1) {
        $r['DESC']['__DATA'] = nl2br(htmlspecialchars($r['DESC']['__DATA'], ENT_COMPAT, 'UTF-8'));
    }

    $r['SHORTDESC']['__DATA'] = $r['SHORTDESC']['__DATA'];
    $r['HINT']['__DATA'] = nl2br(htmlspecialchars($r['HINT']['__DATA'], ENT_COMPAT, 'UTF-8'));

    if (removedObject($r['ID']['__DATA'])) {
        return;
    }

    // cachedesc schon vorhanden?
    $rsDesc = sql(
        "SELECT `id`, `cache_id`, `last_modified`, `language` FROM `cache_desc` WHERE `uuid`='&1'",
        $r['ID']['__DATA']
    );
    if ($rDesc = sql_fetch_array($rsDesc)) {
        if (strtotime($rDesc['last_modified']) < strtotime($r['LASTMODIFIED']['__DATA'])) {
            // cacheid noch die selbe?
            $rsCache = sql("SELECT `uuid` FROM `caches` WHERE cache_id=&1", $rDesc['cache_id']);
            if (!($rCache = sql_fetch_array($rsCache))) {
                importError(
                    'cachedesc',
                    $r['ID']['__DATA'],
                    $r,
                    'Cache does not exist, database inconsistent, skipping'
                );

                return;
            }
            mysql_free_result($rsCache);

            if ($rCache['uuid'] != $r['CACHEID']['__DATA']) {
                importError('cachedesc', $r['ID']['__DATA'], $r, 'Cache changed, not supported');

                return;
            }

            // language geändert?
            if ($rDesc['language'] != $r['LANGUAGE']['__ATTR']['ID']) {
                $rsLang = sql(
                    "SELECT `language` FROM `cache_desc` WHERE `cache_id`=&1 AND `language`='&2'",
                    $rDesc['cache_id'],
                    $r['LANGUAGE']['__ATTR']['ID']
                );
                if (mysql_num_rows($rsLang) > 0) {
                    importError('cachedesc', $r['ID']['__DATA'], $r, 'new language already exists!');

                    return;
                }
                mysql_free_result($rsLang);
            }

            // update
            sql(
                "UPDATE `cache_desc` SET `language`='&1', `desc`='&2', `desc_html`=&3,
                                      `hint`='&4', `short_desc`='&5', `last_modified`='&6'
                                      WHERE `id`=&7 LIMIT 1",
                $r['LANGUAGE']['__ATTR']['ID'],
                $r['DESC']['__DATA'],
                ($r['DESC']['__ATTR']['HTML'] == 1 ? '1' : '0'),
                $r['HINT']['__DATA'],
                $r['SHORTDESC']['__DATA'],
                $r['LASTMODIFIED']['__DATA'],
                $rDesc['id']
            );
        }
    } else {
        // cacheid ermitteln
        $rsCache = sql("SELECT `cache_id` FROM `caches` WHERE `uuid`='&1'", $r['CACHEID']['__DATA']);
        if (!($rCache = sql_fetch_array($rsCache))) {
            importError('cachedesc', $r['ID']['__DATA'], $r, 'Cache does not exist, skipping');

            return;
        }
        mysql_free_result($rsCache);

        // existiert bereits eine beschreibung in der sprache für diesen cache?
        $rsCount = sql(
            "SELECT COUNT(*) `c` FROM `cache_desc` WHERE `cache_id`=&1 AND `language`='&2'",
            $rCache['cache_id'],
            $r['LANGUAGE']['__ATTR']['ID']
        );
        $rCount = sql_fetch_array($rsCount);
        if ($rCount['c'] > 0) {
            importError(
                'cachedesc',
                $r['ID']['__DATA'],
                $r,
                'Cache already has an describtion is this language, skipping'
            );

            return;
        }
        mysql_free_result($rsCount);

        sql(
            "INSERT INTO `cache_desc` (`cache_id`, `language`, `desc`,
                                     `desc_html`, `hint`, `short_desc`,
                                     `last_modified`, `uuid`)
                             VALUES ( &1 , '&2', '&3',
                                      &4 , '&5', '&6',
                                     '&7', '&8')",
            $rCache['cache_id'],
            $r['LANGUAGE']['__ATTR']['ID'],
            $r['DESC']['__DATA'],
            ($r['DESC']['__ATTR']['HTML'] == 1 ? '1' : '0'),
            $r['HINT']['__DATA'],
            $r['SHORTDESC']['__DATA'],
            $r['LASTMODIFIED']['__DATA'],
            $r['ID']['__DATA']
        );
    }
    mysql_free_result($rsDesc);
}

function ImportCachelogArray($r)
{
    /*
        [ID][__DATA] => A2D85008-3F10-1B6F-C97F-01B47AA380F3
        [CACHEID][__DATA] => 42264069-CDD6-5997-104A-AEDA9CEF4E18
        [USERID][__ATTR][UUID] => B97CE517-4D74-B6CE-BED8-EF76662FB7EE
        [LOGTYPE][__ATTR][ID] => 3
        [DATE][__DATA] => 2002-08-25
        [TEXT][__DATA] => > Was hat es denn historisch mit dem Heidentor auf sich ?
        [DATECREATED][__DATA] => 2005-08-14 19:44:01
        [LASTMODIFIED][__DATA] => 2005-08-14 19:44:01
    */
    if (!isset($r['ID']['__DATA']) ||
        !isset($r['CACHEID']['__DATA']) ||
        !isset($r['USERID']['__ATTR']['UUID']) ||
        !isset($r['LOGTYPE']['__ATTR']['ID']) ||
        !isset($r['DATE']['__DATA']) ||
        !isset($r['DATECREATED']['__DATA']) ||
        !isset($r['LASTMODIFIED']['__DATA'])
    ) {
        echo 'error: ImportCachelogArray required element not defined' . "\n";

        return;
    }

    if (!isset($r['TEXT']['__DATA'])) {
        $r['TEXT']['__DATA'] = '';
    }

    if (removedObject($r['ID']['__DATA'])) {
        return;
    }

    // existiert cache?
    $rsCache = sql("SELECT `cache_id` FROM `caches` WHERE `uuid`='&1'", $r['CACHEID']['__DATA']);
    if (!($rCache = sql_fetch_array($rsCache))) {
        importError('cachelog', $r['ID']['__DATA'], $r, 'Cache does not exist, skipping');

        return;
    }

    // existiert user?
    $rsUser = sql("SELECT `user_id` FROM `user` WHERE `uuid`='&1'", $r['USERID']['__ATTR']['UUID']);
    if (!($rUser = sql_fetch_array($rsUser))) {
        importError('cachelog', $r['ID']['__DATA'], $r, 'User does not exist, skipping');

        return;
    }

    // logtype gültig?
    if (sqlValue(
        "SELECT COUNT(*) FROM `log_types` WHERE `id`='" . sql_escape($r['LOGTYPE']['__ATTR']['ID']) . "'",
        0
    ) == 0) {
        importError('cachelog', $r['ID']['__DATA'], $r, 'Logtype not valid, skipping');

        return;
    }

    $rsLog = sql(
        "SELECT `id`, `last_modified`, `user_id`, `cache_id`, `type` FROM `cache_logs` WHERE `uuid`='&1'",
        $r['ID']['__DATA']
    );
    if ($rLog = sql_fetch_array($rsLog)) {
        if (strtotime($rLog['last_modified']) < strtotime($r['LASTMODIFIED']['__DATA'])) {
            if ($rLog['cache_id'] != $rCache['cache_id']) {
                importError('cachelog', $r['ID']['__DATA'], $r, 'Cache_id changed, not supported, skipping');

                return;
            }
            if ($rLog['user_id'] != $rUser['user_id']) {
                importError('cachelog', $r['ID']['__DATA'], $r, 'User_id changed, not supported, skipping');

                return;
            }

            sql(
                "UPDATE `cache_logs` SET `type`=&1, `date`='&2', `text`='&3', `last_modified`='&4', `uuid`='&5', `date_created`='&6' WHERE `id`=&7",
                $r['LOGTYPE']['__ATTR']['ID'],
                date('Y-m-d', strtotime($r['DATE']['__DATA'])),
                $r['TEXT']['__DATA'],
                date('Y-m-d H:i:s', strtotime($r['LASTMODIFIED']['__DATA'])),
                $r['ID']['__DATA'],
                date('Y-m-d H:i:s', strtotime($r['DATECREATED']['__DATA'])),
                $rLog['id']
            );
        }
    } else {
        // log eintragen
        sql(
            "INSERT INTO `cache_logs` (`cache_id`, `user_id`, `type`, `date`, `text`, `last_modified`, `uuid`, `date_created`, `owner_notified`)
                             VALUES (&1        , &2       , &3    , '&4'  , '&5'  , '&6'           , '&7'  , '&8'          , 1)",
            $rCache['cache_id'],
            $rUser['user_id'],
            $r['LOGTYPE']['__ATTR']['ID'],
            date('Y-m-d', strtotime($r['DATE']['__DATA'])),
            $r['TEXT']['__DATA'],
            date('Y-m-d H:i:s', strtotime($r['LASTMODIFIED']['__DATA'])),
            $r['ID']['__DATA'],
            date('Y-m-d H:i:s', strtotime($r['DATECREATED']['__DATA']))
        );
    }
}

function ImportPictureArray($r)
{
    /*
        [ID][__DATA] => DCFDE050-B42F-A76A-E9C7-BCCCC8812A23
        [URL][__DATA] => http://www.opencaching.de/images/uploads/DCFDE050-B42F-A76A-E9C7-BCCCC8812A23.jpg
        [TITLE][__DATA] => Cache
        [OBJECT][__ATTR][TYPE] => 2
        [OBJECT][__DATA] => 42264069-CDD6-5997-104A-AEDA9CEF4E18
        [DATECREATED][__DATA] => 2005-08-20 19:01:37
        [LASTMODIFIED][__DATA] => 2005-08-20 19:01:37
    */
    if (!isset($r['ID']['__DATA']) ||
        !isset($r['URL']['__DATA']) ||
        !isset($r['OBJECT']['__ATTR']['TYPE']) ||
        !isset($r['OBJECT']['__DATA']) ||
        !isset($r['ATTRIBUTES']['__ATTR']['SPOILER']) ||
        !isset($r['ATTRIBUTES']['__ATTR']['DISPLAY']) ||
        !isset($r['DATECREATED']['__DATA']) ||
        !isset($r['LASTMODIFIED']['__DATA'])
    ) {
        echo 'error: ImportPictureArray required element not defined ' . "\n";

        return;
    }

    if (!isset($r['TITLE']['__DATA'])) {
        $r['TITLE']['__DATA'] = '';
    }

    if (removedObject($r['ID']['__DATA'])) {
        return;
    }

    // prüfen, ob object existiert und user_id ermitteln
    switch ($r['OBJECT']['__ATTR']['TYPE']) {
        case 1:
            $rsObject = sql(
                "SELECT `id` `object_id`, `user_id` FROM `cache_logs` WHERE `uuid`='&1'",
                $r['OBJECT']['__DATA']
            );
            break;
        case 2:
            $rsObject = sql(
                "SELECT `cache_id` `object_id`, `user_id` FROM `caches` WHERE `uuid`='&1'",
                $r['OBJECT']['__DATA']
            );
            break;
        default:
            importError('picture', $r['ID']['__DATA'], $r, 'object_type, not supported, skipping');

            return;
    }
    $rObject = sql_fetch_array($rsObject);
    mysql_free_result($rsObject);

    if ($rObject == false) {
        importError('picture', $r['ID']['__DATA'], $r, 'object not found, skipping');

        return;
    }

    $rsPicture = sql(
        "SELECT `id`, `object_id`, `object_type`, `last_modified`, `user_id` FROM `pictures` WHERE `uuid`='&1'",
        $r['ID']['__DATA']
    );
    if ($rPicture = sql_fetch_array($rsPicture)) {
        if (strtotime($rPicture['last_modified']) < strtotime($r['LASTMODIFIED']['__DATA'])) {
            // user_id noch gleich?
            if ($rPicture['user_id'] != $rObject['user_id']) {
                importError('picture', $r['ID']['__DATA'], $r, 'user_id changed, not supported, skipping');

                return;
            }

            // object noch das selbe?
            if (($rPicture['object_id'] != $rObject['object_id']) || ($rPicture['object_type'] != $r['OBJECT']['__ATTR']['TYPE'])) {
                importError('picture', $r['ID']['__DATA'], $r, 'object changed, not supported, skipping');

                return;
            }

            sql(
                "UPDATE `pictures` SET `url`='&1', `last_modified`='&2', `title`='&3', `date_created`='&4', `spoiler`=&5, `local`=0, `display`=&6 WHERE `id`=&7",
                $r['URL']['__DATA'],
                date('Y-m-d H:i:s', strtotime($r['LASTMODIFIED']['__DATA'])),
                $r['TITLE']['__DATA'],
                date('Y-m-d H:i:s', strtotime($r['DATECREATED']['__DATA'])),
                $r['ATTRIBUTES']['__ATTR']['SPOILER'],
                $r['ATTRIBUTES']['__ATTR']['DISPLAY'],
                $rPicture['id']
            );
        }
    } else {
        sql(
            "INSERT INTO `pictures` (`uuid`, `url`, `last_modified`, `title`, `date_created`, `object_id`, `object_type`, `user_id`, `spoiler`, `local`, `display`)
                           VALUES ('&1'  , '&2' , '&3'           , '&4'   , '&5'          , &6         , &7           , &8       , &9       , 0      , &10)",
            $r['ID']['__DATA'],
            $r['URL']['__DATA'],
            date('Y-m-d H:i:s', strtotime($r['LASTMODIFIED']['__DATA'])),
            $r['TITLE']['__DATA'],
            date('Y-m-d H:i:s', strtotime($r['DATECREATED']['__DATA'])),
            $rObject['object_id'],
            $r['OBJECT']['__ATTR']['TYPE'],
            $rObject['user_id'],
            $r['ATTRIBUTES']['__ATTR']['SPOILER'],
            $r['ATTRIBUTES']['__ATTR']['DISPLAY']
        );
    }
}

function ImportRemovedObjectArray($r)
{
    /*
        [OBJECT][__ATTR][TYPE] => 1
        [OBJECT][__DATA] => CA1FCA8F-DEED-06D8-F971-53634CC91AEC
        [REMOVEDDATE][__DATA] => 2005-08-14 19:31:32
    */
    if (!isset($r['OBJECT']['__ATTR']['TYPE']) ||
        !isset($r['OBJECT']['__DATA']) ||
        !isset($r['REMOVEDDATE']['__DATA'])
    ) {
        echo 'error: ImportRemovedObjectArray required element not defined' . "\n";

        return;
    }

    $localid = 0;
    switch ($r['OBJECT']['__ATTR']['TYPE']) {
        case 1:
            // cachelog
            $rsLog = sql(
                "SELECT `cache_id`, `user_id`, `type` FROM `cache_logs` WHERE `uuid`='&1'",
                $r['OBJECT']['__DATA']
            );
            if ($rLog = sql_fetch_array($rsLog)) {
                sql("DELETE FROM `cache_logs` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            }

            break;
        case 2:
            // cache
            $rsCache = sql("SELECT `user_id` FROM `caches` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            if ($rCache = sql_fetch_array($rsCache)) {
                sql("DELETE FROM `caches` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            }
            break;
        case 3:
            // cachedesc
            $rsDesc = sql("SELECT `cache_id` FROM `cache_desc` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            if ($rDesc = sql_fetch_array($rsDesc)) {
                sql("DELETE FROM `cache_desc` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            }
            break;
        case 4:
            // user
            $rsUser = sql("SELECT `user_id` FROM `user` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            if ($rUser = sql_fetch_array($rsUser)) {
                sql("DELETE FROM `user` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
            }
            break;
        case 6:
            // picture
            $rsPicture = sql(
                "SELECT `object_id`, `object_type` FROM `pictures` WHERE `uuid`='&1'",
                $r['OBJECT']['__DATA']
            );

            if ($rPicture = sql_fetch_array($rsPicture)) {
                sql("DELETE FROM `pictures` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);

                switch ($rPicture['object_type']) {
                    case 1:
                        $rsCounter = sql(
                            "SELECT COUNT(*) `count` FROM `pictures` WHERE `object_type`=1 AND `object_id`=&1",
                            $rPicture['object_id']
                        );
                        $rCounter = sql_fetch_array($rsCounter);
                        mysql_free_result($rsCounter);

                        break;
                    case 2:
                        $rsCounter = sql(
                            "SELECT COUNT(*) `count` FROM `pictures` WHERE `object_type`=2 AND `object_id`=&1",
                            $rPicture['object_id']
                        );
                        $rCounter = sql_fetch_array($rsCounter);
                        mysql_free_result($rsCounter);

                        break;
                    default:
                        importError(
                            'removedobject (picture)',
                            $r['OBJECT']['__DATA'],
                            $r,
                            'object type not supported, skipping'
                        );

                        return;
                }
            }
            break;
        default:
            importError('removedobject', $r['OBJECT']['__DATA'], $r, 'object type not supported, skipping');

            return;
    }

    // in removed_object einfügen ...
    $rs = sql("SELECT * FROM `removed_objects` WHERE `uuid`='&1'", $r['OBJECT']['__DATA']);
    if (mysql_num_rows($rs) == 0) {
        sql(
            "INSERT INTO `removed_objects` (`uuid`, `localid`, `type`, `removed_date`)
                                VALUES ('&1', &2, &3, '&4')",
            $r['OBJECT']['__DATA'],
            $localid,
            $r['OBJECT']['__ATTR']['TYPE'],
            date('Y-m-d H:i:s', strtotime($r['REMOVEDDATE']['__DATA']))
        );
    }
}

function removedObject($uuid)
{
    $rs = sql("SELECT `id` FROM `removed_objects` WHERE `uuid`='&1'", $uuid);
    if ($r = sql_fetch_array($rs)) {
        return true;
    } else {
        return false;
    }
}

function importError($recordtype, $uuid, $r, $info)
{
    echo 'error: ' . $recordtype . ' (' . $uuid . '): ' . $info . "\n";
}

function importWarn($recordtype, $uuid, $r, $info)
{
    echo 'warn: ' . $recordtype . ' (' . $uuid . '): ' . $info . "\n";
}

function restorevalues()
{
    $rs = sql(
        'SELECT `replication_overwritetypes`.`table` `table`, `replication_overwritetypes`.`field` `field`, `replication_overwritetypes`.`uuid_fieldname` `uuid_fieldname`, `replication_overwrite`.`value` `value`, `replication_overwrite`.`uuid` `uuid` FROM `replication_overwrite`, `replication_overwritetypes` WHERE `replication_overwrite`.`type` = `replication_overwritetypes`.`id`'
    );
    while ($r = sql_fetch_array($rs)) {
        sql(
            "UPDATE `&1` SET `&2`='&3' WHERE `&4`='&5' LIMIT 1",
            $r['table'],
            $r['field'],
            $r['value'],
            $r['uuid_fieldname'],
            $r['uuid']
        );
    }
    mysql_free_result($rs);
}
