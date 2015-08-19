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
 *             Only set the following keys in $opt[]
 *
 *                 $opt['template']['locales']
 *                 $opt['geokrety']['locales']
 *                 $opt['locale']
 ***************************************************************************/

	/* Locale definitions
	 *
	 */
	$opt['template']['locales']['DE']['show'] = true;
	$opt['template']['locales']['DE']['flag'] = 'images/flag/DE.png';
	$opt['template']['locales']['DE']['name'] = 'Deutsch';
	$opt['template']['locales']['EN']['show'] = true;
	$opt['template']['locales']['EN']['flag'] = 'images/flag/EN.png';
	$opt['template']['locales']['EN']['name'] = 'English';
	$opt['template']['locales']['IT']['show'] = true;
	$opt['template']['locales']['IT']['flag'] = 'images/flag/IT.png';
	$opt['template']['locales']['IT']['name'] = 'Italiano';
	$opt['template']['locales']['ES']['show'] = true;
	$opt['template']['locales']['ES']['flag'] = 'images/flag/ES.png';
	$opt['template']['locales']['ES']['name'] = 'Español';
	$opt['template']['locales']['FR']['show'] = true;
	$opt['template']['locales']['FR']['flag'] = 'images/flag/FR.png';
	$opt['template']['locales']['FR']['name'] = 'Français';

	$opt['template']['locales']['SV']['show'] = true; // sv_SE
	$opt['template']['locales']['SV']['flag'] = 'images/flag/SE.png';
	$opt['template']['locales']['SV']['name'] = 'Svenska';
	$opt['template']['locales']['NO']['show'] = true; // no_NO
	$opt['template']['locales']['NO']['flag'] = 'images/flag/NO.png';
	$opt['template']['locales']['NO']['name'] = 'Norsk';
	$opt['template']['locales']['NL']['show'] = true;
	$opt['template']['locales']['NL']['flag'] = 'images/flag/NL.png';
	$opt['template']['locales']['NL']['name'] = 'Nederlands';
	$opt['template']['locales']['PL']['show'] = true;
	$opt['template']['locales']['PL']['flag'] = 'images/flag/PL.png';
	$opt['template']['locales']['PL']['name'] = 'Polski';
	$opt['template']['locales']['RU']['show'] = true;
	$opt['template']['locales']['RU']['flag'] = 'images/flag/RU.png';
	$opt['template']['locales']['RU']['name'] = 'Русский';
	$opt['template']['locales']['DA']['show'] = true; // da_DK
	$opt['template']['locales']['DA']['flag'] = 'images/flags/dk.png';
	$opt['template']['locales']['DA']['name'] = 'Danske';
	$opt['template']['locales']['PT']['show'] = true; // pt_PT
	$opt['template']['locales']['PT']['flag'] = 'images/flags/pt.png';
	$opt['template']['locales']['PT']['name'] = 'Portuguesa';
	$opt['template']['locales']['JA']['show'] = true; // ja_JP
	$opt['template']['locales']['JA']['flag'] = 'images/flag/JP.png';
	$opt['template']['locales']['JA']['name'] = '日本語';

	// geokrety language key association
	$opt['geokrety']['locales']['DE'] = 'de_DE.UTF-8';
	$opt['geokrety']['locales']['EN'] = 'en_EN';
	$opt['geokrety']['locales']['IT'] = 'it_IT.UTF-8';
	$opt['geokrety']['locales']['ES'] = 'es_ES.UTF-8';
	$opt['geokrety']['locales']['FR'] = 'fr_FR.UTF-8';

	$opt['geokrety']['locales']['SV'] = 'sv_SE.UTF-8';
	$opt['geokrety']['locales']['NO'] = 'no_NO.UTF-8';
	$opt['geokrety']['locales']['NL'] = 'en_EN';
	$opt['geokrety']['locales']['PL'] = 'pl_PL.UTF-8';
	$opt['geokrety']['locales']['RU'] = 'en_EN';
	$opt['geokrety']['locales']['DA'] = 'da_DK.UTF-8';
	$opt['geokrety']['locales']['PT'] = 'pt_PT.UTF-8';
	$opt['geokrety']['locales']['JA'] = 'ja_JP.UTF-8';

	$opt['locale']['DE']['locales'] = array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge');
	$opt['locale']['EN']['locales'] = array('en_US.utf8', 'en_US', 'en');
	$opt['locale']['IT']['locales'] = array('it_IT.utf8', 'it_IT', 'it');
	$opt['locale']['ES']['locales'] = array('es_ES.utf8', 'es_ES', 'es');
	$opt['locale']['FR']['locales'] = array('fr_FR.utf8', 'fr_FR@euro', 'fr_FR', 'french', 'fr');

	$opt['locale']['SV']['locales'] = array('sv_SE.utf8', 'sv_SE', 'se');
	$opt['locale']['NO']['locales'] = array('no_NO.utf8', 'no_NO', 'no');
	$opt['locale']['PL']['locales'] = array('pl_PL.utf8', 'pl_PL', 'pl');
	$opt['locale']['NL']['locales'] = array('nl_NL.utf8', 'nl_NL', 'nl');
	$opt['locale']['RU']['locales'] = array('ru_RU.utf8', 'ru_RU', 'ru');
	$opt['locale']['DA']['locales'] = array('da_DK.utf8', 'da_DK', 'dk');
	$opt['locale']['PT']['locales'] = array('pt_PT.utf8', 'pt_PT', 'pt');
	$opt['locale']['JA']['locales'] = array('ja_JP.utf8', 'ja_JP', 'jp');

	$opt['locale']['EN']['format']['dm'] = '%m/%d';
	$opt['locale']['EN']['format']['dateshort'] = '%m/%d/%y';
	$opt['locale']['EN']['format']['date'] = '%x';
	$opt['locale']['EN']['format']['datelong'] = '%B %d, %Y';
	$opt['locale']['EN']['format']['datetime'] = '%x %I:%M %p';
	$opt['locale']['EN']['format']['datetimesec'] = '%x %X';
	$opt['locale']['EN']['format']['time'] = '%I:%M %p';
	$opt['locale']['EN']['format']['timesec'] = '%X';
	$opt['locale']['EN']['format']['phpdate'] = 'm/d/Y';
	$opt['locale']['EN']['format']['dot1000'] = ',';
	$opt['locale']['EN']['format']['colonspace'] = '';
	$opt['locale']['EN']['country'] = 'UK';
	$opt['locale']['EN']['page']['subtitle1'] = 'Geocaching with Opencaching';
	$opt['locale']['EN']['page']['subtitle2'] = '';
	$opt['locale']['EN']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.en';
	$opt['locale']['EN']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons License Terms" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">The Opencaching.de <a href="articles.php?page=impressum#datalicense">content</a> is licensed under Creative Commons <a rel="license" href="%1" target="_blank">BY-BC-ND 3.0 DE</a>.</div>';
	$opt['locale']['EN']['helpwiki'] = "http://wiki.opencaching.de/index.php/";

	$opt['locale']['DE']['format']['dm'] = '%d.%m.';
	$opt['locale']['DE']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['DE']['format']['date'] = '%x';
	$opt['locale']['DE']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['DE']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['DE']['format']['datetimesec'] = '%x %X';
	$opt['locale']['DE']['format']['time'] = '%H:%M';
	$opt['locale']['DE']['format']['timesec'] = '%X';
	$opt['locale']['DE']['format']['phpdate'] = 'd.m.Y';
	$opt['locale']['DE']['format']['colonspace'] = '';
	$opt['locale']['DE']['country'] = 'DE';
	$opt['locale']['DE']['page']['subtitle1'] = 'Geocaching mit Opencaching';
	$opt['locale']['DE']['page']['subtitle2'] = '';
	$opt['locale']['DE']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/';
	$opt['locale']['DE']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons Lizenzvertrag" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Die <a href="articles.php?page=impressum#datalicense">Inhalte</a> von Opencaching.de stehen unter der Creative-Commons-Lizenz <a rel="license" href="%1">BY-NC-ND 3.0 DE</a>.</div>';
	$opt['locale']['DE']['helpwiki'] = "http://wiki.opencaching.de/index.php/";

	$opt['locale']['IT']['format']['dateshort'] = '%d/%m/%y';
	$opt['locale']['IT']['format']['dm'] = '%d/%m';
	$opt['locale']['IT']['format']['date'] = '%x';
	$opt['locale']['IT']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['IT']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['IT']['format']['datetimesec'] = '%x %X';
	$opt['locale']['IT']['format']['time'] = '%H:%M';
	$opt['locale']['IT']['format']['timesec'] = '%X';
	$opt['locale']['IT']['format']['phpdate'] = 'd/m/Y';
	$opt['locale']['IT']['format']['colonspace'] = '';
	$opt['locale']['IT']['country'] = 'IT';
	$opt['locale']['IT']['page']['subtitle1'] = 'Geocaching con Opencaching';
	$opt['locale']['IT']['page']['subtitle2'] = '';
	$opt['locale']['IT']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.it';
	$opt['locale']['IT']['page']['license'] = '<a rel="license" href="%1" target="_blank"><img alt="Creative Commons License Terms" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Il <a href="articles.php?page=impressum#datalicense">contenuto</a> di Opencaching.de è rilasciato sotto licenza Creative Commons <a rel="license" href="%1" target="_blank">BY-NC-ND 3.0 DE</a>.</div>';

	$opt['locale']['ES']['format']['dateshort'] = '%d/%m/%y';
	$opt['locale']['ES']['format']['dm'] = '%d/%m';
	$opt['locale']['ES']['format']['date'] = '%x';
	$opt['locale']['ES']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['ES']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['ES']['format']['datetimesec'] = '%x %X';
	$opt['locale']['ES']['format']['time'] = '%H:%M';
	$opt['locale']['ES']['format']['timesec'] = '%X';
	$opt['locale']['ES']['format']['phpdate'] = 'd/m/Y';
	$opt['locale']['ES']['format']['colonspace'] = '';
	$opt['locale']['ES']['country'] = 'ES';
	$opt['locale']['ES']['page']['subtitle1'] = 'Geocaching con Opencaching';
	$opt['locale']['ES']['page']['subtitle2'] = '';
	$opt['locale']['ES']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.es_ES';
	$opt['locale']['ES']['page']['license'] = '<a rel="license" href="%1" target="_blank"><img alt="Creative Commons License Terms" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">El <a href="articles.php?page=impressum#datalicense">contenido</a> está disponible bajo Creative Commons <a rel="license" href="%1" target="_blank">BY-NC-ND 3.0 DE</a> licencia.</div>';

	$opt['locale']['FR']['format']['dm'] = '%d.%m.';
	$opt['locale']['FR']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['FR']['format']['date'] = '%x';
	$opt['locale']['FR']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['FR']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['FR']['format']['datetimesec'] = '%x %X';
	$opt['locale']['FR']['format']['time'] = '%H:%M';
	$opt['locale']['FR']['format']['timesec'] = '%X';
	$opt['locale']['FR']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['FR']['format']['colonspace'] = ' ';
	$opt['locale']['FR']['country'] = 'FR';
	$opt['locale']['FR']['page']['license_url'] = 'http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.fr';
	$opt['locale']['FR']['page']['license'] = '<a rel="license" href="%1"><img alt="Creative Commons License Terms" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/de/88x31.png" /></a><div style="text-align:center; margin:8px 0 0 6px;">Le<a  href="articles.php?page=impressum#datalicense">contenu</a> de Opencaching.de sont sous licence Creative Commons <a rel="license" href="%1" target="_blank">BY-BC-ND 3.0 DE</a>.</div>';
	$opt['locale']['FR']['helpwiki'] = "http://wiki.opencaching.de/index.php/";

	$opt['locale']['SV']['format']['dateshort'] = '%y-%m-%d';
	$opt['locale']['SV']['format']['dm'] = '%d/%m';
	$opt['locale']['SV']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['SV']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['SV']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['SV']['format']['datetimesec'] = '%x %X';
	$opt['locale']['SV']['format']['time'] = '%H:%M';
	$opt['locale']['SV']['format']['timesec'] = '%X';
	$opt['locale']['SV']['format']['phpdate'] = 'Y-m-d';
	$opt['locale']['SV']['format']['colonspace'] = '';
	$opt['locale']['SV']['country'] = 'SE';
	$opt['locale']['SV']['page']['subtitle1'] = 'Geocaching med Opencaching';
	$opt['locale']['SV']['page']['subtitle2'] = '';

	$opt['locale']['NO']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['NO']['format']['dm'] = '%d.%m.';
	$opt['locale']['NO']['format']['date'] = '%x';
	$opt['locale']['NO']['format']['datelong'] = '%x';
	$opt['locale']['NO']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['NO']['format']['datetimesec'] = '%x %X';
	$opt['locale']['NO']['format']['time'] = '%H:%M';
	$opt['locale']['NO']['format']['timesec'] = '%X';
	$opt['locale']['NO']['format']['phpdate'] = 'd.m.Y';
	$opt['locale']['NO']['format']['colonspace'] = '';
	$opt['locale']['NO']['country'] = 'NO';
	$opt['locale']['NO']['page']['subtitle1'] = 'Geocaching med Opencaching';
	$opt['locale']['NO']['page']['subtitle2'] = '';

	$opt['locale']['PL']['format']['dm'] = '%d.%m.';
	$opt['locale']['PL']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['PL']['format']['date'] = '%x';
	$opt['locale']['PL']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['PL']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['PL']['format']['datetimesec'] = '%x %X';
	$opt['locale']['PL']['format']['time'] = '%H:%M';
	$opt['locale']['PL']['format']['timesec'] = '%X';
	$opt['locale']['PL']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['PL']['format']['colonspace'] = '';
	$opt['locale']['PL']['country'] = 'PL';

	$opt['locale']['NL']['format']['dm'] = '%d.%m.';
	$opt['locale']['NL']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['NL']['format']['date'] = '%x';
	$opt['locale']['NL']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['NL']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['NL']['format']['datetimesec'] = '%x %X';
	$opt['locale']['NL']['format']['time'] = '%H:%M';
	$opt['locale']['NL']['format']['timesec'] = '%X';
	$opt['locale']['NL']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['NL']['format']['colonspace'] = '';
	$opt['locale']['NL']['page']['subtitle1'] = 'Geocaching met Opencaching';
	$opt['locale']['NL']['page']['subtitle2'] = '';
	$opt['locale']['NL']['country'] = 'NL';

	$opt['locale']['RU']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['RU']['format']['dm'] = '%d.%m.';
	$opt['locale']['RU']['format']['date'] = '%x';
	$opt['locale']['RU']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['RU']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['RU']['format']['datetimesec'] = '%x %X';
	$opt['locale']['RU']['format']['time'] = '%H:%M';
	$opt['locale']['RU']['format']['timesec'] = '%X';
	$opt['locale']['RU']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['RU']['format']['colonspace'] = '';
	$opt['locale']['RU']['country'] = 'RU';

	$opt['locale']['DA']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['DA']['format']['dm'] = '%d.%m.';
	$opt['locale']['DA']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['DA']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['DA']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['DA']['format']['datetimesec'] = '%x %X';
	$opt['locale']['DA']['format']['time'] = '%H:%M';
	$opt['locale']['DA']['format']['timesec'] = '%X';
	$opt['locale']['DA']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['DE']['format']['colonspace'] = '';
	$opt['locale']['DA']['country'] = 'DK';
	$opt['locale']['DA']['page']['subtitle1'] = 'Geocaching med Opencaching';
	$opt['locale']['DA']['page']['subtitle2'] = '';

	$opt['locale']['PT']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['PT']['format']['dm'] = '%d.%m.';
	$opt['locale']['PT']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['PT']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['PT']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['PT']['format']['datetimesec'] = '%x %X';
	$opt['locale']['PT']['format']['time'] = '%H:%M';
	$opt['locale']['PT']['format']['timesec'] = '%X';
	$opt['locale']['PT']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['PT']['format']['colonspace'] = '';
	$opt['locale']['PT']['country'] = 'PT';
	$opt['locale']['PT']['page']['subtitle1'] = 'Geocaching com Opencaching';
	$opt['locale']['PT']['page']['subtitle2'] = '';

	$opt['locale']['JA']['format']['dateshort'] = '%d.%m.%y';
	$opt['locale']['JA']['format']['dm'] = '%d.%m.';
	$opt['locale']['JA']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['JA']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['JA']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['JA']['format']['datetimesec'] = '%x %X';
	$opt['locale']['JA']['format']['time'] = '%H:%M';
	$opt['locale']['JA']['format']['timesec'] = '%X';
	$opt['locale']['JA']['format']['phpdate'] = 'd-m-Y';
	$opt['locale']['JA']['format']['colonspace'] = '';
	$opt['locale']['JA']['country'] = 'JP';
	$opt['locale']['JA']['page']['subtitle1'] = 'Opencachingとジオキャッシング';
	$opt['locale']['JA']['page']['subtitle2'] = '';

?>
