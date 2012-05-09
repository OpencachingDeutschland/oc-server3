<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Default settings for all options in settings.inc.php
 *  Do not modify this file - use settings.inc.php!
 ***************************************************************************/


	/* Locale definitions
	 *
	 */
	$opt['template']['locales']['DE']['show'] = true;
	$opt['template']['locales']['DE']['flag'] = 'images/flags/de.gif';
	$opt['template']['locales']['DE']['name'] = 'Deutsch';
	$opt['template']['locales']['FR']['show'] = true;
	$opt['template']['locales']['FR']['flag'] = 'images/flags/fr.gif';
	$opt['template']['locales']['FR']['name'] = 'Français';
	$opt['template']['locales']['NL']['show'] = true;
	$opt['template']['locales']['NL']['flag'] = 'images/flags/nl.gif';
	$opt['template']['locales']['NL']['name'] = 'Nederlands';
	$opt['template']['locales']['EN']['show'] = true;
	$opt['template']['locales']['EN']['flag'] = 'images/flags/en.gif';
	$opt['template']['locales']['EN']['name'] = 'English';
	$opt['template']['locales']['PL']['show'] = true;
	$opt['template']['locales']['PL']['flag'] = 'images/flags/pl.gif';
	$opt['template']['locales']['PL']['name'] = 'Polski';
	$opt['template']['locales']['IT']['show'] = true;
	$opt['template']['locales']['IT']['flag'] = 'images/flags/it.gif';
	$opt['template']['locales']['IT']['name'] = 'Italiano';
	$opt['template']['locales']['RU']['show'] = true;
	$opt['template']['locales']['RU']['flag'] = 'images/flags/ru.gif';
	$opt['template']['locales']['RU']['name'] = 'Русский';
	$opt['template']['locales']['ES']['show'] = true;
	$opt['template']['locales']['ES']['flag'] = 'images/flags/es.gif';
	$opt['template']['locales']['ES']['name'] = 'Español';
	$opt['template']['locales']['SV']['show'] = true; // sv_SE
	$opt['template']['locales']['SV']['flag'] = 'images/flags/se.gif';
	$opt['template']['locales']['SV']['name'] = 'Svenska';
	$opt['template']['locales']['NO']['show'] = true; // no_NO
	$opt['template']['locales']['NO']['flag'] = 'images/flags/no.gif';
	$opt['template']['locales']['NO']['name'] = 'Norsk';
	$opt['template']['locales']['DA']['show'] = true; // da_DK
	$opt['template']['locales']['DA']['flag'] = 'images/flags/dk.gif';
	$opt['template']['locales']['DA']['name'] = 'Danske';
	$opt['template']['locales']['PT']['show'] = true; // pt_PT
	$opt['template']['locales']['PT']['flag'] = 'images/flags/pt.gif';
	$opt['template']['locales']['PT']['name'] = 'Portuguesa';
	$opt['template']['locales']['JA']['show'] = true; // ja_JP
	$opt['template']['locales']['JA']['flag'] = 'images/flags/jp.gif';
	$opt['template']['locales']['JA']['name'] = '日本語';

	// geokrety language key association
	$opt['geokrety']['locales']['DE'] = 'de_DE.UTF-8';
	$opt['geokrety']['locales']['EN'] = 'en_EN';
	$opt['geokrety']['locales']['FR'] = 'fr_FR.UTF-8';
	$opt['geokrety']['locales']['NL'] = 'en_EN';
	$opt['geokrety']['locales']['PL'] = 'pl_PL.UTF-8';
	$opt['geokrety']['locales']['IT'] = 'en_EN';
	$opt['geokrety']['locales']['RU'] = 'en_EN';
	$opt['geokrety']['locales']['ES'] = 'es_ES.UTF-8';
	$opt['geokrety']['locales']['SV'] = 'sv_SE.UTF-8';
	$opt['geokrety']['locales']['NO'] = 'no_NO.UTF-8';
	$opt['geokrety']['locales']['DA'] = 'da_DK.UTF-8';
	$opt['geokrety']['locales']['PT'] = 'pt_PT.UTF-8';
	$opt['geokrety']['locales']['JA'] = 'ja_JP.UTF-8';

	$opt['locale']['DE']['locales'] = array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge');
	$opt['locale']['EN']['locales'] = array('en_US.utf8', 'en_US', 'en');
	$opt['locale']['FR']['locales'] = array('fr_FR.utf8', 'fr_FR@euro', 'fr_FR', 'french', 'fr');
	$opt['locale']['PL']['locales'] = array('pl_PL.utf8', 'pl_PL', 'pl');
	$opt['locale']['NL']['locales'] = array('nl_NL.utf8', 'nl_NL', 'nl');
	$opt['locale']['IT']['locales'] = array('it_IT.utf8', 'it_IT', 'it');
	$opt['locale']['RU']['locales'] = array('ru_RU.utf8', 'ru_RU', 'ru');
	$opt['locale']['ES']['locales'] = array('es_ES.utf8', 'es_ES', 'es');
	$opt['locale']['SV']['locales'] = array('sv_SE.utf8', 'sv_SE', 'se');
	$opt['locale']['NO']['locales'] = array('no_NO.utf8', 'no_NO', 'no');
	$opt['locale']['DA']['locales'] = array('da_DK.utf8', 'da_DK', 'dk');
	$opt['locale']['PT']['locales'] = array('pt_PT.utf8', 'pt_PT', 'pt');
	$opt['locale']['JA']['locales'] = array('ja_JP.utf8', 'ja_JP', 'jp');

	$opt['locale']['EN']['format']['date'] = '%x';
	$opt['locale']['EN']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['EN']['format']['datetime'] = '%x %I:%M %p';
	$opt['locale']['EN']['format']['datetimesec'] = '%x %X';
	$opt['locale']['EN']['format']['time'] = '%I:%M %p';
	$opt['locale']['EN']['format']['timesec'] = '%X';
	$opt['locale']['EN']['country'] = 'UK';

	$opt['locale']['DE']['format']['date'] = '%x';
	$opt['locale']['DE']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['DE']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['DE']['format']['datetimesec'] = '%x %X';
	$opt['locale']['DE']['format']['time'] = '%H:%M';
	$opt['locale']['DE']['format']['timesec'] = '%X';
	$opt['locale']['DE']['country'] = 'DE';
	$opt['locale']['DE']['page']['subtitle1'] = 'Geocaching in Deutschland,';
	$opt['locale']['DE']['page']['subtitle2'] = 'Österreich und der Schweiz';

	$opt['locale']['FR']['format']['date'] = '%x';
	$opt['locale']['FR']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['FR']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['FR']['format']['datetimesec'] = '%x %X';
	$opt['locale']['FR']['format']['time'] = '%H:%M';
	$opt['locale']['FR']['format']['timesec'] = '%X';
	$opt['locale']['FR']['country'] = 'FR';

	$opt['locale']['PL']['format']['date'] = '%x';
	$opt['locale']['PL']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['PL']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['PL']['format']['datetimesec'] = '%x %X';
	$opt['locale']['PL']['format']['time'] = '%H:%M';
	$opt['locale']['PL']['format']['timesec'] = '%X';
	$opt['locale']['PL']['country'] = 'PL';

	$opt['locale']['NL']['format']['date'] = '%x';
	$opt['locale']['NL']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['NL']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['NL']['format']['datetimesec'] = '%x %X';
	$opt['locale']['NL']['format']['time'] = '%H:%M';
	$opt['locale']['NL']['format']['timesec'] = '%X';
	$opt['locale']['NL']['page']['subtitle1'] = 'Geocaching met Opencaching';
	$opt['locale']['NL']['page']['subtitle2'] = '';
	$opt['locale']['NL']['country'] = 'NL';

	$opt['locale']['IT']['format']['date'] = '%x';
	$opt['locale']['IT']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['IT']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['IT']['format']['datetimesec'] = '%x %X';
	$opt['locale']['IT']['format']['time'] = '%H:%M';
	$opt['locale']['IT']['format']['timesec'] = '%X';
	$opt['locale']['IT']['country'] = 'IT';
	$opt['locale']['IT']['page']['subtitle1'] = 'Geocaching con Opencaching';
	$opt['locale']['IT']['page']['subtitle2'] = '';

	$opt['locale']['RU']['format']['date'] = '%x';
	$opt['locale']['RU']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['RU']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['RU']['format']['datetimesec'] = '%x %X';
	$opt['locale']['RU']['format']['time'] = '%H:%M';
	$opt['locale']['RU']['format']['timesec'] = '%X';
	$opt['locale']['RU']['country'] = 'RU';

	$opt['locale']['ES']['format']['date'] = '%x';
	$opt['locale']['ES']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['ES']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['ES']['format']['datetimesec'] = '%x %X';
	$opt['locale']['ES']['format']['time'] = '%H:%M';
	$opt['locale']['ES']['format']['timesec'] = '%X';
	$opt['locale']['ES']['country'] = 'ES';
	$opt['locale']['ES']['page']['subtitle1'] = 'Geocaching con Opencaching';
	$opt['locale']['ES']['page']['subtitle2'] = '';

	$opt['locale']['SV']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['SV']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['SV']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['SV']['format']['datetimesec'] = '%x %X';
	$opt['locale']['SV']['format']['time'] = '%H:%M';
	$opt['locale']['SV']['format']['timesec'] = '%X';
	$opt['locale']['SV']['country'] = 'SE';
	$opt['locale']['SV']['page']['subtitle1'] = 'Geocaching med Opencaching';
	$opt['locale']['SV']['page']['subtitle2'] = '';

	$opt['locale']['NO']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['NO']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['NO']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['NO']['format']['datetimesec'] = '%x %X';
	$opt['locale']['NO']['format']['time'] = '%H:%M';
	$opt['locale']['NO']['format']['timesec'] = '%X';
	$opt['locale']['NO']['country'] = 'NO';
	$opt['locale']['NO']['page']['subtitle1'] = 'Geocaching med Opencaching';
	$opt['locale']['NO']['page']['subtitle2'] = '';

	$opt['locale']['DA']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['DA']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['DA']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['DA']['format']['datetimesec'] = '%x %X';
	$opt['locale']['DA']['format']['time'] = '%H:%M';
	$opt['locale']['DA']['format']['timesec'] = '%X';
	$opt['locale']['DA']['country'] = 'DK';
	$opt['locale']['DA']['page']['subtitle1'] = 'Geocaching med Opencaching';
	$opt['locale']['DA']['page']['subtitle2'] = '';

	$opt['locale']['PT']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['PT']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['PT']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['PT']['format']['datetimesec'] = '%x %X';
	$opt['locale']['PT']['format']['time'] = '%H:%M';
	$opt['locale']['PT']['format']['timesec'] = '%X';
	$opt['locale']['PT']['country'] = 'PT';
	$opt['locale']['PT']['page']['subtitle1'] = 'Geocaching com Opencaching';
	$opt['locale']['PT']['page']['subtitle2'] = '';

	$opt['locale']['JA']['format']['date'] = '%Y-%m-%d';
	$opt['locale']['JA']['format']['datelong'] = '%Y-%m-%d';
	$opt['locale']['JA']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['JA']['format']['datetimesec'] = '%x %X';
	$opt['locale']['JA']['format']['time'] = '%H:%M';
	$opt['locale']['JA']['format']['timesec'] = '%X';
	$opt['locale']['JA']['country'] = 'JP';
	$opt['locale']['JA']['page']['subtitle1'] = 'Opencachingとジオキャッシング';
	$opt['locale']['JA']['page']['subtitle2'] = '';

?>
