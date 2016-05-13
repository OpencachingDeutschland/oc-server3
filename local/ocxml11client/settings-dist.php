<?php
// Unicode Reminder メモ

// safemode-unzip
$opt['unzip'] = '/srv/www/htdocs/www.opencaching.de/html/util/safemode_zip/phpunzip.php';
$opt['rel_tmpdir'] = 'util/ocxml11client/tmp';

// 2. DB z.B. als Backup einer sauberen DB
$opt['db'][1]['name'] = 'ocdublette';
$opt['db'][1]['username'] = $dbusername;
$opt['db'][1]['passwd'] = $dbpasswd;
$opt['db'][1]['server'] = $dbserver;

// Synchronisierungsoptionen
$opt['sync']['user'] = 1;
$opt['sync']['cache'] = 1;
$opt['sync']['cachedesc'] = 1;
$opt['sync']['cachelog'] = 1;
$opt['sync']['picture'] = 1;
$opt['sync']['picturefromcachelog'] = 1;
$opt['sync']['removedobject'] = 1;

// Bilder downloaden?
$opt['pictures']['download'] = 1;
$opt['pictures']['directory'] = $rootpath . 'images/uploads/';
$opt['pictures']['url'] = 'http://www.so-komm-ich-uebers-web-da.hin/www.opencaching.de/html/images/uploads/';

// Sessions verwenden?
$opt['session'] = 1;
$opt['zip'] = 'gzip';                // 0; zip; bzip2; gzip

// Gebietsauswahl nach Land
$opt['bycountry'] = 0;
$opt['country'] = 'DE';

// Gebietsauswahl nach Koordinaten
$opt['bycoords'] = 0;
$opt['lon'] = 50.12345;
$opt['lat'] = 9.12345;
$opt['distance'] = 150;

// sonstige Einstellungen
$opt['tmpdir'] = 'tmp/';
$opt['archivdir'] = 'data-files/';
$opt['url'] = 'http://www.opencaching.de/xml/ocxml11.php?modifiedsince={modifiedsince}&user={user}&cache={cache}&cachedesc={cachedesc}&cachelog={cachelog}&picture={picture}&picturefromcachelog={picturefromcachelog}&removedobject={removedobject}&session={session}&zip={zip}&charset=utf-8';
$opt['urlappend_country'] = '&country={country}';
$opt['urlappend_coords'] = '&lon={lon}&lat={lat}&distance={distance}';
$opt['url_getsession'] = 'http://www.opencaching.de/xml/ocxml11.php?sessionid={sessionid}&file={file}&zip={zip}&charset=utf-8';
