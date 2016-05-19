<?php
/***************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Script zum ändern der Owners
 * Schutz über htpasswd!
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$rootpath = __DIR__ . '/../../';
require_once $rootpath . 'lib/common.inc.php';
require_once $rootpath . 'lib/eventhandler.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($action == 'changeowner') {
    $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
    $newuserid = isset($_REQUEST['newuserid']) ? $_REQUEST['newuserid'] : 0;
    $commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;

    if ($commit != '1') {
        die('Kein commit!');
    }

    $rsCache = sql(
        "SELECT caches.cache_id, user.user_id
        FROM caches
        INNER JOIN user ON caches.user_id=user.user_id
        WHERE caches.cache_id='&1'",
        $cacheid
    );
    $rCache = sql_fetch_assoc($rsCache);
    sql_free_result($rsCache);
    $rsUser = sql("SELECT user_id FROM user WHERE user_id='&1'", $newuserid);
    $rUser = sql_fetch_assoc($rsUser);
    sql_free_result($rsUser);

    if ($rCache === false) {
        die('Cache existiert nicht!');
    }
    if ($rUser === false) {
        die('User existiert nicht!');
    }

    sql("UPDATE caches SET user_id='&1' WHERE cache_id='&2'", $rUser['user_id'], $rCache['cache_id']);

    // send event to delete statpic
    event_change_statpic($rCache['user_id']);
    event_change_statpic($rUser['user_id']);

    echo 'Besitzer geändert';

    // logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
    logentry('approving', 4, 0, $cacheid, 0, 'Owner changed ' . $cacheid, '');

    exit;
} elseif ($action == 'showcache') {
    $wp = isset($_REQUEST['waypoint']) ? $_REQUEST['waypoint'] : 0;
    $newusername = isset($_REQUEST['newusername']) ? $_REQUEST['newusername'] : 0;

    $cacheid = sqlValue("SELECT cache_id FROM caches WHERE wp_oc='" . sql_escape($wp) . "'", 0);
    $userid = sqlValue("SELECT user_id FROM user WHERE username='" . sql_escape($newusername) . "'", '');

    if ($cacheid == 0) {
        die('Cache nicht gefunden!');
    }
    if ($userid == 0) {
        die('User nicht gefunden!');
    }

    $rsUser = sql("SELECT user_id, username FROM user WHERE user_id='&1'", $userid);
    $rUser = sql_fetch_assoc($rsUser);
    sql_free_result($rsUser);

    $rsCache = sql(
        "SELECT caches.cache_id, caches.wp_oc, caches.name, user.username
        FROM caches
        INNER JOIN user ON caches.user_id=user.user_id
        WHERE caches.cache_id='&1'",
        $cacheid
    );
    $rCache = sql_fetch_assoc($rsCache);
    sql_free_result($rsCache);
    ?>
    <html>
    <body>
    <h2>Cacheowner ändern</h2>
    <form action="cache.php" method="get">
        <input type="hidden" name="action" value="changeowner"/>
        <input type="hidden" name="cacheid" value="<?php echo htmlspecialchars($rCache['cache_id']); ?>"/>
        <input type="hidden" name="newuserid" value="<?php echo htmlspecialchars($rUser['user_id']); ?>"/>
        <table>
            <tr>
                <td>Wegpunkt</td>
                <td><?php echo htmlspecialchars($rCache['wp_oc']); ?></td>
            </tr>
            <tr>
                <td>Name</td>
                <td><?php echo htmlspecialchars($rCache['name']); ?></td>
            </tr>
            <tr>
                <td>Benutzer</td>
                <td><?php echo htmlspecialchars($rCache['username']); ?></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>Neuer Benutzer</td>
                <td><?php echo htmlspecialchars($rUser['username']); ?></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><input type="checkbox" name="commit" value="1"/> Sicher?</td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="Auswählen"/></td>
            </tr>
        </table>
    </form>
    </body>
    </html>
    <?php
    exit;
}
?>
<html>
<body>
<form action="cache.php" method="get">
    <input type="hidden" name="action" value="showcache"/>
    <table>
        <tr>
            <td>Wegpunkt des Cache</td>
            <td><input type="text" name="waypoint" size="8" value="OC"/></td>
        </tr>
        <td>Benutzername des neuen Owner</td>
        <td><input type="text" name="newusername" size="25"/></td>
        <tr>
        <tr>
            <td colspan="2"><input type="submit" value="Auswählen"/></td>
        </tr>
    </table>
</form>
</body>
</html>
