<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *  remove a cache log
 *
 *  GET/POST-Parameter: logid
 *
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

if (isset($_REQUEST['userid'])) {
    $get_id = $_REQUEST['userid'];
} else {
    echo "No UserID given";
    exit;
}

$connection = AppKernel::Container()->get(Connection::class);

echo "<h2>Löschprotokoll nach DSGVO</h2>";
//Hole Userdaten
$query = $connection->query("SELECT user_id, username FROM user WHERE user_id=$get_id");
$get_user = $query->fetchObject();
$num_rows = $query->rowCount();

if ($num_rows < 1) {
    echo "Es existiert kein Benutzer mit dieser ID " . $get_id . "";
    exit;
} else {
    $user_id = $get_user->user_id;
    $user_name = $get_user->username;
    echo "Benutzer mit der ID " . $user_id . " | " . $user_name . " gefunden.";
}

$eigene_logs = $connection->fetchAll("SELECT uuid, cache_id FROM cache_logs WHERE user_id = $user_id");

$bilder_count = 0;
foreach ($eigene_logs AS $log) {
    $logid = $log['cache_id'];

    $log_bilder = $connection->fetchAll(
        "SELECT pics.uuid, pics.url, pics.thumb_url FROM pictures AS pics INNER JOIN cache_logs logs ON logs.id = pics.object_id
         WHERE object_type = 1 AND logs.user_id = $user_id AND cache_id = $logid "
    );
    if (count($log_bilder) >= 1) {
        $bilder_count ++;
        $bilder_container[] = $log_bilder[0];
    };
}

$eigene_caches = $connection->fetchAll("SELECT cache_id FROM caches WHERE user_id = $user_id");

$cache_beschreibung = 0;
foreach ($eigene_caches as $cache) {
    $cache_id = $cache['cache_id'];
    $cache_desc = $connection->fetchAll("SELECT uuid FROM cache_desc WHERE cache_id=$cache_id");
    $cache_beschreibung ++;
}

$eigene_modifizierte_caches =
    $connection->fetchAll("SELECT cache_id FROM cache_desc_modified WHERE cache_id=$cache_id");

$ignorierte_caches = $connection->fetchAll("SELECT cache_id, user_id FROM cache_ignore WHERE user_id = $user_id");
$eigene_empfehlungen = $connection->fetchAll("SELECT cache_id, user_id FROM cache_rating WHERE user_id = $user_id");
$fieldnotes = $connection->fetchAll("SELECT id FROM field_note WHERE user_id = $user_id");
$adoptionen = $connection->fetchAll("SELECT user_id FROM cache_adoption WHERE user_id = $user_id");
$beobachtungen = $connection->fetchAll("SELECT user_id FROM cache_watches WHERE user_id = $user_id");
$bookmarklisten = $connection->fetchAll("SELECT user_id FROM cache_list_bookmarks WHERE user_id = $user_id");
$cache_bookmark_watches = $connection->fetchAll("SELECT user_id FROM cache_list_watches WHERE user_id = $user_id");
$cache_lists = $connection->fetchAll("SELECT user_id FROM cache_lists WHERE user_id = $user_id");
$cache_reports = $connection->fetchAll("SELECT userid FROM cache_reports WHERE userid=$user_id");
$email_protokolle = $connection->fetchAll("SELECT * FROM email_user WHERE from_user_id=$user_id");
$email_protokolle2 = $connection->fetchAll("SELECT * FROM email_user WHERE to_user_id=$user_id");

echo "<h4>Datenbestand</h4>";
echo "
<table class='table'>
<tr>
<td>Tabelle</td><td>Anzahl</td><td>Zweck</td>
</tr>";
echo "<tr><td>cache_logs</td><td>" . count($eigene_logs) . "</td><td>Logeinträge gefunden.</td></tr>";
echo "<tr><td VALIGN='top'>pictures</td><td  VALIGN='top'>" . $bilder_count . "</td><td> Logbilder gefunden.";
if ($bilder_container) {
    ECHO "<br>";
    foreach ($bilder_container as $bild_uuid) {
        echo $bild_uuid['uuid'];
        echo " &nbsp; <a href='" . $bild_uuid['url'] . "'>L</a><br>";
    }
}
ECHO "</td></tr>";
echo "<tr><td>caches</td><td>" . count($eigene_caches) . "</td><td> eigene Caches gefunden.</td></tr>";
echo "<tr><td>cache_desc</td><td>" . $cache_beschreibung . "</td><td> Cache Beschreibungen gefunden.</td></tr>";
echo "<tr><td>cache_desc_modified</td><td>"
     . count($eigene_modifizierte_caches)
     . "</td><td> modifizierten Daten gefunden.</td></tr>";
echo "<tr><td>cache_ignore</td><td>" . count($ignorierte_caches) . "</td><td> ignorierte Caches.</td></tr>";
echo "<tr><td>cache_rating</td><td>" . count($eigene_empfehlungen) . "</td><td> Empfehlungen gefunden.</td></tr>";
echo "<tr><td>field_note</td><td>" . count($fieldnotes) . "</td><td> Fieldnotes gefunden.</td></tr>";
echo "<tr><td>cache_adoption</td><td>" . count($adoptionen) . "</td><td> Adoptionen gefunden.</td></tr>";
echo "<tr><td>cache_watches</td><td>" . count($beobachtungen) . "</td><td> Beobachtungen gefunden.</td></tr>";
echo "<tr><td>cache_list_bookmarks</td><td>" . count($bookmarklisten) . "</td><td> Bookmark Listen gefunden.</td></tr>";
echo "<tr><td>cache_list_watches</td><td>"
     . count($cache_bookmark_watches)
     . "</td><td> Bookmark Watch Listen gefunden.</td></tr>";
echo "<tr><td>cache_lists</td><td>" . count($cache_lists) . "</td><td> Cache Listen gefunden.</td></tr>";
echo "<tr><td>cache_reports</td><td>" . count($cache_reports) . "</td><td> Cachemeldungen gefunden.</td></tr>";
echo "<tr><td>email_user</td><td>"
     . count($email_protokolle)
     . "<br>"
     . count($email_protokolle2)
     . "</td><td>Email gesendete Protokolle<br>Email Empfangsprotokolle</td></tr>";
echo "</table>";

echo "<hr>
        <h4>Löschung und Anonymisierung durchgeführt</h4>";

//if (count($bilder_count) >= 1) {
//    foreach ($bilder_container as $bild_uuid) {
//        $filename = $bild_uuid['uuid'];
//        unlink('images/uploads/'.$filename.'.jpg');
//    }
//};

//$picture = new picture();
//foreach ($bilder_container as $bild_uuid) {
//    if ($picture->delete($bild_uuid) == false) {
//        $tpl->error(ERROR_NO_ACCESS);
//    }
//}

$sql = "DELETE FROM cache_ignore WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Ignorierungen gelöscht<br>";
} else {
    echo "Ignorierungen bereits gelöscht<br>";
}

$sql = "DELETE FROM field_note WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Fieldnotes gelöscht<br>";
} else {
    echo "Fieldnotes bereits gelöscht<br>";
}

$sql = "DELETE FROM cache_adoption WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Adoptionen gelöscht<br>";
} else {
    echo "Adoptionen bereits gelöscht<br>";
}

$sql = "DELETE FROM cache_watches WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Beobachtungen gelöscht<br>";
} else {
    echo "Beobachtungen bereits gelöscht<br>";
}

$sql = "DELETE FROM cache_list_bookmarks WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Bookmarklisten gelöscht<br>";
} else {
    echo "Bookmarklisten bereits gelöscht<br>";
}

$sql = "DELETE FROM cache_list_watches WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Bookmark Watchlisten gelöscht<br>";
} else {
    echo "Bookmark Watchlisten bereits gelöscht<br>";
}

$sql = "DELETE FROM cache_lists WHERE user_id = $user_id";
if ($connection->query($sql) === true) {
    echo "Cachelisten gelöscht<br>";
} else {
    echo "Cachelisten bereits gelöscht<br>";
}

$sql = "DELETE FROM cache_reports WHERE userid=$user_id";
if ($connection->query($sql) === true) {
    echo "Email gesendete Protokolle gelöscht<br>";
} else {
    echo "Email Sendeprotokoll bereits gelöscht<br>";
}

$sql = "DELETE FROM email_user WHERE from_user_id=$user_id";
if ($connection->query($sql) === true) {
    echo "Email gesendete Protokolle gelöscht<br>";
} else {
    echo "Email Versandprotokolle bereits gelöscht<br>";
}

$sql = "DELETE FROM email_user WHERE to_user_id=$user_id";
if ($connection->query($sql) === true) {
    echo "Email Empfangsprotokolle gelöscht<br>";
} else {
    echo "Email Empfangsprotokolle bereits gelöscht<br>";
}

echo "<hr>
    Auszug vom " . date("d.m.Y H:i");