<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Default settings for all locale options in settings.inc.php
 *  Do not modify this file - use settings.inc.php!
 *
 *  ATTENTION: This file is also used in old template system.
 *             (this means any call to framework functions may be incompatible)
 *
 *             Only set the following keys in $opt[]:
 *
 *                 $opt['template']['locales']
 *                 $opt['geokrety']['locales']
 *                 $opt['locale']
 ***************************************************************************/

define('OC_LOCALE_ACTIVE', 2);    // enable language and show language button in header line
define('OC_LOCALE_HIDDEN', 1);    // enable language but hide language button in header line
define('OC_LOCALE_DISABLED', 0);  //  disable language

$opt['template']['locales']['DE']['status'] = OC_LOCALE_ACTIVE;
$opt['template']['locales']['DE']['flag'] = 'images/flag/DE.png';
$opt['template']['locales']['DE']['name'] = 'Deutsch';
$opt['template']['locales']['EN']['status'] = OC_LOCALE_ACTIVE;
$opt['template']['locales']['EN']['flag'] = 'images/flag/EN.png';
$opt['template']['locales']['EN']['name'] = 'English';
$opt['template']['locales']['IT']['status'] = OC_LOCALE_ACTIVE;
$opt['template']['locales']['IT']['flag'] = 'images/flag/IT.png';
$opt['template']['locales']['IT']['name'] = 'Italiano';
$opt['template']['locales']['ES']['status'] = OC_LOCALE_ACTIVE;
$opt['template']['locales']['ES']['flag'] = 'images/flag/ES.png';
$opt['template']['locales']['ES']['name'] = 'Español';
$opt['template']['locales']['FR']['status'] = OC_LOCALE_ACTIVE;
$opt['template']['locales']['FR']['flag'] = 'images/flag/FR.png';
$opt['template']['locales']['FR']['name'] = 'Français';
$opt['template']['locales']['NL']['status'] = OC_LOCALE_HIDDEN;
$opt['template']['locales']['NL']['flag'] = 'images/flag/NL.png';
$opt['template']['locales']['NL']['name'] = 'Nederlands';
$opt['template']['locales']['SV']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['SV']['flag'] = 'images/flag/SE.png';
$opt['template']['locales']['SV']['name'] = 'Svenska';
$opt['template']['locales']['NO']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['NO']['flag'] = 'images/flag/NO.png';
$opt['template']['locales']['NO']['name'] = 'Norsk';
$opt['template']['locales']['PL']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['PL']['flag'] = 'images/flag/PL.png';
$opt['template']['locales']['PL']['name'] = 'Polski';
$opt['template']['locales']['RU']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['RU']['flag'] = 'images/flag/RU.png';
$opt['template']['locales']['RU']['name'] = 'Русский';
$opt['template']['locales']['DA']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['DA']['flag'] = 'images/flags/dk.png';
$opt['template']['locales']['DA']['name'] = 'Danske';
$opt['template']['locales']['PT']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['PT']['flag'] = 'images/flags/pt.png';
$opt['template']['locales']['PT']['name'] = 'Portuguesa';
$opt['template']['locales']['JA']['status'] = OC_LOCALE_DISABLED;
$opt['template']['locales']['JA']['flag'] = 'images/flag/JP.png';
$opt['template']['locales']['JA']['name'] = '日本語';

// geokrety language key association
$opt['geokrety']['locales']['DE'] = 'de_DE.UTF-8';
$opt['geokrety']['locales']['EN'] = 'en_EN';
$opt['geokrety']['locales']['IT'] = 'it_IT.UTF-8';
$opt['geokrety']['locales']['ES'] = 'es_ES.UTF-8';
$opt['geokrety']['locales']['FR'] = 'fr_FR.UTF-8';
$opt['geokrety']['locales']['NL'] = 'en_EN';
$opt['geokrety']['locales']['SV'] = 'sv_SE.UTF-8';
$opt['geokrety']['locales']['NO'] = 'no_NO.UTF-8';
$opt['geokrety']['locales']['PL'] = 'pl_PL.UTF-8';
$opt['geokrety']['locales']['RU'] = 'en_EN';
$opt['geokrety']['locales']['DA'] = 'da_DK.UTF-8';
$opt['geokrety']['locales']['PT'] = 'pt_PT.UTF-8';
$opt['geokrety']['locales']['JA'] = 'ja_JP.UTF-8';

$opt['locale']['DE']['locales'] = ['de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge'];
$opt['locale']['EN']['locales'] = ['en_US.utf8', 'en_US', 'en'];
$opt['locale']['IT']['locales'] = ['it_IT.utf8', 'it_IT', 'it'];
$opt['locale']['ES']['locales'] = ['es_ES.utf8', 'es_ES', 'es'];
$opt['locale']['FR']['locales'] = ['fr_FR.utf8', 'fr_FR@euro', 'fr_FR', 'french', 'fr'];
$opt['locale']['NL']['locales'] = ['nl_NL.utf8', 'nl_NL', 'nl'];
$opt['locale']['SV']['locales'] = ['sv_SE.utf8', 'sv_SE', 'se'];
$opt['locale']['NO']['locales'] = ['no_NO.utf8', 'no_NO', 'no'];
$opt['locale']['PL']['locales'] = ['pl_PL.utf8', 'pl_PL', 'pl'];
$opt['locale']['RU']['locales'] = ['ru_RU.utf8', 'ru_RU', 'ru'];
$opt['locale']['DA']['locales'] = ['da_DK.utf8', 'da_DK', 'dk'];
$opt['locale']['PT']['locales'] = ['pt_PT.utf8', 'pt_PT', 'pt'];
$opt['locale']['JA']['locales'] = ['ja_JP.utf8', 'ja_JP', 'jp'];

$opt['locale']['EN']['format']['dm'] = '%d/%m';
$opt['locale']['EN']['format']['dateshort'] = '%d/%m/%y';
$opt['locale']['EN']['format']['date'] = '%Y-%d-%m';
$opt['locale']['EN']['format']['datelong'] = '%d %B %Y';
$opt['locale']['EN']['format']['datetime'] = '%Y-%d-%m %I:%M %p';
$opt['locale']['EN']['format']['datetimesec'] = '%Y-%d-%m %X';
$opt['locale']['EN']['format']['time'] = '%I:%M %p';
$opt['locale']['EN']['format']['timesec'] = '%X';
$opt['locale']['EN']['format']['phpdate'] = 'Y-m-d';
$opt['locale']['EN']['format']['phpdatetime'] = 'Y-m-d h:i A';
$opt['locale']['EN']['format']['dot1000'] = ',';
$opt['locale']['EN']['format']['colonspace'] = '';
$opt['locale']['EN']['country'] = 'GB';
$opt['locale']['EN']['primary_lang_of'] = ['GB', 'US', 'AU', 'NZ', 'ZA', 'IE', 'SG'];
$opt['locale']['EN']['page']['subtitle1'] = 'Geocaching with Opencaching';
$opt['locale']['EN']['page']['subtitle2'] = '';
$opt['locale']['EN']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.en';
$opt['locale']['EN']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons License Terms" style="border-width:0" src="resource2/ocstyle/images/media/cc-by-nc-nd-small.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">The {site} <a href="articles.php?page=impressum#datalicense">content</a> is licensed under Creative Commons <a rel="license" href="%1" target="_blank">BY-BC-ND 3.0 DE</a>.</div>';
$opt['locale']['EN']['helpwiki'] = 'http://wiki.opencaching.de/index.php/';
$opt['locale']['EN']['mostly_translated'] = true;
$opt['locale']['EN']['what3words'] = true;

$opt['locale']['DE']['format']['dm'] = '%d.%m.';
$opt['locale']['DE']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['DE']['format']['date'] = '%x';
$opt['locale']['DE']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['DE']['format']['datetime'] = '%x %H:%M';
$opt['locale']['DE']['format']['datetimesec'] = '%x %X';
$opt['locale']['DE']['format']['time'] = '%H:%M';
$opt['locale']['DE']['format']['timesec'] = '%X';
$opt['locale']['DE']['format']['phpdate'] = 'd.m.Y';
$opt['locale']['DE']['format']['phpdatetime'] = 'd.m.Y H:i';
$opt['locale']['DE']['format']['colonspace'] = '';
$opt['locale']['DE']['country'] = 'DE';
$opt['locale']['DE']['primary_lang_of'] = ['DE', 'AT', 'CH', 'LI'];
$opt['locale']['DE']['page']['subtitle1'] = 'Geocaching mit Opencaching';
$opt['locale']['DE']['page']['subtitle2'] = '';
$opt['locale']['DE']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/';
$opt['locale']['DE']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons Lizenzvertrag" style="border-width:0" src="resource2/ocstyle/images/media/cc-by-nc-nd-small.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Die <a href="articles.php?page=impressum#datalicense">Inhalte</a> von {site} stehen unter der Creative-Commons-Lizenz <a rel="license" href="%1">BY-NC-ND 3.0 DE</a>.</div>';
$opt['locale']['DE']['helpwiki'] = 'http://wiki.opencaching.de/index.php/';
$opt['locale']['DE']['mostly_translated'] = true;
$opt['locale']['DE']['what3words'] = true;  // "beta"

$opt['locale']['IT']['format']['dateshort'] = '%d/%m/%y';
$opt['locale']['IT']['format']['dm'] = '%d/%m';
$opt['locale']['IT']['format']['date'] = '%x';
$opt['locale']['IT']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['IT']['format']['datetime'] = '%x %H:%M';
$opt['locale']['IT']['format']['datetimesec'] = '%x %X';
$opt['locale']['IT']['format']['time'] = '%H:%M';
$opt['locale']['IT']['format']['timesec'] = '%X';
$opt['locale']['IT']['format']['phpdate'] = 'd/m/Y';
$opt['locale']['IT']['format']['phpdatetime'] = 'd/m/Y H:i';
$opt['locale']['IT']['format']['colonspace'] = '';
$opt['locale']['IT']['country'] = 'IT';
$opt['locale']['IT']['primary_lang_of'] = ['IT', 'SM', 'VA'];
$opt['locale']['IT']['page']['subtitle1'] = 'Geocaching con Opencaching';
$opt['locale']['IT']['page']['subtitle2'] = '';
$opt['locale']['IT']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.it';
$opt['locale']['IT']['page']['license'] = '<a rel="license" href="%1" target="_blank"><img alt="Creative Commons License Terms" style="border-width:0" src="resource2/ocstyle/images/media/cc-by-nc-nd-small.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Il <a href="articles.php?page=impressum#datalicense">contenuto</a> di {site} è rilasciato sotto licenza Creative Commons <a rel="license" href="%1" target="_blank">BY-NC-ND 3.0 DE</a>.</div>';
$opt['locale']['IT']['mostly_translated'] = true;
$opt['locale']['IT']['what3words'] = false;

$opt['locale']['ES']['format']['dateshort'] = '%d/%m/%y';
$opt['locale']['ES']['format']['dm'] = '%d/%m';
$opt['locale']['ES']['format']['date'] = '%x';
$opt['locale']['ES']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['ES']['format']['datetime'] = '%x %H:%M';
$opt['locale']['ES']['format']['datetimesec'] = '%x %X';
$opt['locale']['ES']['format']['time'] = '%H:%M';
$opt['locale']['ES']['format']['timesec'] = '%X';
$opt['locale']['ES']['format']['phpdate'] = 'd/m/Y';
$opt['locale']['ES']['format']['phpdatetime'] = 'd/m/Y H:i';
$opt['locale']['ES']['format']['colonspace'] = '';
$opt['locale']['ES']['country'] = 'ES';
$opt['locale']['ES']['primary_lang_of'] =
    ['ES', 'AR', 'CL', 'CR', 'DO', 'EC', 'SV', 'GT', 'HN', 'CO', 'CU', 'MX', 'NI', 'PA', 'PY', 'PE', 'UY', 'VE'];
$opt['locale']['ES']['page']['subtitle1'] = 'Geocaching con Opencaching';
$opt['locale']['ES']['page']['subtitle2'] = '';
$opt['locale']['ES']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.es_ES';
$opt['locale']['ES']['page']['license'] = '<a rel="license" href="%1" target="_blank"><img alt="Creative Commons License Terms" style="border-width:0" src="resource2/ocstyle/images/media/cc-by-nc-nd-small.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">El <a href="articles.php?page=impressum#datalicense">contenido</a> de {site} está disponible bajo Creative Commons <a rel="license" href="%1" target="_blank">BY-NC-ND 3.0 DE</a> licencia.</div>';
$opt['locale']['ES']['mostly_translated'] = true;
$opt['locale']['ES']['what3words'] = true;

$opt['locale']['FR']['format']['dm'] = '%d.%m.';
$opt['locale']['FR']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['FR']['format']['date'] = '%x';
$opt['locale']['FR']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['FR']['format']['datetime'] = '%x %H:%M';
$opt['locale']['FR']['format']['datetimesec'] = '%x %X';
$opt['locale']['FR']['format']['time'] = '%H:%M';
$opt['locale']['FR']['format']['timesec'] = '%X';
$opt['locale']['FR']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['FR']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['FR']['format']['colonspace'] = ' ';
$opt['locale']['FR']['country'] = 'FR';
$opt['locale']['FR']['primary_lang_of'] = ['FR'];
$opt['locale']['FR']['page']['subtitle1'] = 'Geocaching avec Opencaching';
$opt['locale']['FR']['page']['subtitle2'] = '';
$opt['locale']['FR']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.fr';
$opt['locale']['FR']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons License Terms" style="border-width:0" src="resource2/ocstyle/images/media/cc-by-nc-nd-small.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Le<a  href="articles.php?page=impressum#datalicense">contenu</a> de {site} sont sous licence Creative Commons <a rel="license" href="%1" target="_blank">BY-BC-ND 3.0 DE</a>.</div>';
$opt['locale']['FR']['helpwiki'] = 'http://wiki.opencaching.de/index.php/';
$opt['locale']['FR']['mostly_translated'] = true;
$opt['locale']['FR']['what3words'] = true;

$opt['locale']['NL']['format']['dm'] = '%d.%m.';
$opt['locale']['NL']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['NL']['format']['date'] = '%x';
$opt['locale']['NL']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['NL']['format']['datetime'] = '%x %H:%M';
$opt['locale']['NL']['format']['datetimesec'] = '%x %X';
$opt['locale']['NL']['format']['time'] = '%H:%M';
$opt['locale']['NL']['format']['timesec'] = '%X';
$opt['locale']['NL']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['NL']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['NL']['format']['colonspace'] = '';
$opt['locale']['NL']['country'] = 'NL';
$opt['locale']['NL']['primary_lang_of'] = ['NL'];
$opt['locale']['NL']['page']['subtitle1'] = 'Geocaching met Opencaching';
$opt['locale']['NL']['page']['subtitle2'] = '';
$opt['locale']['NL']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/';
$opt['locale']['NL']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons Lizenzvertrag" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Die <a href="articles.php?page=impressum#datalicense">Inhalte</a> von Opencaching.de stehen unter der Creative-Commons-Lizenz <a rel="license" href="%1">BY-NC-ND 3.0 DE</a>.</div>';
$opt['locale']['NL']['helpwiki'] = 'http://wiki.opencaching.de/index.php/';
$opt['locale']['NL']['country'] = 'NL';
$opt['locale']['NL']['mostly_translated'] = false;
$opt['locale']['NL']['what3words'] = false;

$opt['locale']['SV']['format']['dateshort'] = '%y-%m-%d';
$opt['locale']['SV']['format']['dm'] = '%d/%m';
$opt['locale']['SV']['format']['date'] = '%Y-%m-%d';
$opt['locale']['SV']['format']['datelong'] = '%Y-%m-%d';
$opt['locale']['SV']['format']['datetime'] = '%x %H:%M';
$opt['locale']['SV']['format']['datetimesec'] = '%x %X';
$opt['locale']['SV']['format']['time'] = '%H:%M';
$opt['locale']['SV']['format']['timesec'] = '%X';
$opt['locale']['SV']['format']['phpdate'] = 'Y-m-d';
$opt['locale']['SV']['format']['phpdatetime'] = 'Y-m-d H:i';
$opt['locale']['SV']['format']['colonspace'] = '';
$opt['locale']['SV']['country'] = 'SE';
$opt['locale']['SV']['primary_lang_of'] = ['SV'];
$opt['locale']['SV']['page']['subtitle1'] = 'Geocaching med Opencaching';
$opt['locale']['SV']['page']['subtitle2'] = '';
$opt['locale']['SV']['mostly_translated'] = false;
$opt['locale']['SV']['what3words'] = true;  // "beta"

$opt['locale']['NO']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['NO']['format']['dm'] = '%d.%m.';
$opt['locale']['NO']['format']['date'] = '%x';
$opt['locale']['NO']['format']['datelong'] = '%x';
$opt['locale']['NO']['format']['datetime'] = '%x %H:%M';
$opt['locale']['NO']['format']['datetimesec'] = '%x %X';
$opt['locale']['NO']['format']['time'] = '%H:%M';
$opt['locale']['NO']['format']['timesec'] = '%X';
$opt['locale']['NO']['format']['phpdate'] = 'd.m.Y';
$opt['locale']['NO']['format']['phpdatetime'] = 'd.m.Y H:i';
$opt['locale']['NO']['format']['colonspace'] = '';
$opt['locale']['NO']['country'] = 'NO';
$opt['locale']['NO']['primary_lang_of'] = ['NO'];
$opt['locale']['NO']['page']['subtitle1'] = 'Geocaching med Opencaching';
$opt['locale']['NO']['page']['subtitle2'] = '';
$opt['locale']['NO']['mostly_translated'] = false;
$opt['locale']['NO']['what3words'] = false;

$opt['locale']['PL']['format']['dm'] = '%d.%m.';
$opt['locale']['PL']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['PL']['format']['date'] = '%x';
$opt['locale']['PL']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['PL']['format']['datetime'] = '%x %H:%M';
$opt['locale']['PL']['format']['datetimesec'] = '%x %X';
$opt['locale']['PL']['format']['time'] = '%H:%M';
$opt['locale']['PL']['format']['timesec'] = '%X';
$opt['locale']['PL']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['PL']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['PL']['format']['colonspace'] = '';
$opt['locale']['PL']['country'] = 'PL';
$opt['locale']['PL']['primary_lang_of'] = ['PL'];
$opt['locale']['PL']['mostly_translated'] = false;
$opt['locale']['PL']['what3words'] = false;

$opt['locale']['RU']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['RU']['format']['dm'] = '%d.%m.';
$opt['locale']['RU']['format']['date'] = '%x';
$opt['locale']['RU']['format']['datelong'] = '%d. %B %Y';
$opt['locale']['RU']['format']['datetime'] = '%x %H:%M';
$opt['locale']['RU']['format']['datetimesec'] = '%x %X';
$opt['locale']['RU']['format']['time'] = '%H:%M';
$opt['locale']['RU']['format']['timesec'] = '%X';
$opt['locale']['RU']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['RU']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['RU']['format']['colonspace'] = '';
$opt['locale']['RU']['country'] = 'RU';
$opt['locale']['RU']['primary_lang_of'] = ['RU'];
$opt['locale']['RU']['mostly_translated'] = false;
$opt['locale']['RU']['what3words'] = true;   // "beta"

$opt['locale']['DA']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['DA']['format']['dm'] = '%d.%m.';
$opt['locale']['DA']['format']['date'] = '%Y-%m-%d';
$opt['locale']['DA']['format']['datelong'] = '%Y-%m-%d';
$opt['locale']['DA']['format']['datetime'] = '%x %H:%M';
$opt['locale']['DA']['format']['datetimesec'] = '%x %X';
$opt['locale']['DA']['format']['time'] = '%H:%M';
$opt['locale']['DA']['format']['timesec'] = '%X';
$opt['locale']['DA']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['DA']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['DA']['format']['colonspace'] = '';
$opt['locale']['DA']['country'] = 'DK';
$opt['locale']['RU']['primary_lang_of'] = ['DK', 'GL'];
$opt['locale']['DA']['page']['subtitle1'] = 'Geocaching med Opencaching';
$opt['locale']['DA']['page']['subtitle2'] = '';
$opt['locale']['DA']['mostly_translated'] = false;
$opt['locale']['DA']['what3words'] = false;

$opt['locale']['PT']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['PT']['format']['dm'] = '%d.%m.';
$opt['locale']['PT']['format']['date'] = '%Y-%m-%d';
$opt['locale']['PT']['format']['datelong'] = '%Y-%m-%d';
$opt['locale']['PT']['format']['datetime'] = '%x %H:%M';
$opt['locale']['PT']['format']['datetimesec'] = '%x %X';
$opt['locale']['PT']['format']['time'] = '%H:%M';
$opt['locale']['PT']['format']['timesec'] = '%X';
$opt['locale']['PT']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['PT']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['PT']['format']['colonspace'] = '';
$opt['locale']['PT']['country'] = 'PT';
$opt['locale']['PT']['primary_lang_of'] = ['PT', 'AO', 'BR', 'CV', 'GW', 'MZ', 'ST'];
$opt['locale']['PT']['page']['subtitle1'] = 'Geocaching com Opencaching';
$opt['locale']['PT']['page']['subtitle2'] = '';
$opt['locale']['PT']['mostly_translated'] = false;
$opt['locale']['PT']['what3words'] = true;

$opt['locale']['JA']['format']['dateshort'] = '%d.%m.%y';
$opt['locale']['JA']['format']['dm'] = '%d.%m.';
$opt['locale']['JA']['format']['date'] = '%Y-%m-%d';
$opt['locale']['JA']['format']['datelong'] = '%Y-%m-%d';
$opt['locale']['JA']['format']['datetime'] = '%x %H:%M';
$opt['locale']['JA']['format']['datetimesec'] = '%x %X';
$opt['locale']['JA']['format']['time'] = '%H:%M';
$opt['locale']['JA']['format']['timesec'] = '%X';
$opt['locale']['JA']['format']['phpdate'] = 'd-m-Y';
$opt['locale']['JA']['format']['phpdatetime'] = 'd-m-Y H:i';
$opt['locale']['JA']['format']['colonspace'] = '';
$opt['locale']['JA']['country'] = 'JP';
$opt['locale']['RU']['primary_lang_of'] = ['JP'];
$opt['locale']['JA']['page']['subtitle1'] = 'Opencachingとジオキャッシング';
$opt['locale']['JA']['page']['subtitle2'] = '';
$opt['locale']['JA']['mostly_translated'] = false;
$opt['locale']['JA']['what3words'] = false;

function set_php_locale()
{
    global $opt;

    setlocale(LC_MONETARY, $opt['locale'][$opt['template']['locale']]['locales']);
    setlocale(LC_TIME, $opt['locale'][$opt['template']['locale']]['locales']);
    if (defined('LC_MESSAGES')) {
        setlocale(LC_MESSAGES, $opt['locale'][$opt['template']['locale']]['locales']);
    }

    // no localisation!
    setlocale(LC_COLLATE, $opt['locale']['EN']['locales']);
    setlocale(LC_CTYPE, $opt['locale']['EN']['locales']);
    setlocale(LC_NUMERIC, $opt['locale']['EN']['locales']); // important for mysql-queries!
}
