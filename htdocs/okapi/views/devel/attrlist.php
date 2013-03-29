<?php

namespace okapi\views\devel\attrlist;

use Exception;
use okapi\Okapi;
use okapi\Cache;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\OkapiRedirectResponse;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\Settings;

class View
{
	public static function call()
	{
		# This is a hidden page for OKAPI developers. It will list all
		# attributes defined in this OC installation (and some other stuff).

		ob_start();

		print "Cache Types:\n\n";
		foreach (self::get_all_cachetypes() as $id => $name)
			print "$id: $name\n";

		print "\nLog Types:\n\n";
		foreach (self::get_all_logtypes() as $id => $name)
			print "$id: $name\n";

		print "\nAttributes:\n\n";
		$dict = Okapi::get_all_atribute_names();
		foreach ($dict as $internal_id => $langs)
		{
			print $internal_id.": ";
			$langkeys = array_keys($langs);
			sort($langkeys);
			if (in_array('en', $langkeys))
				print strtoupper($langs['en'])."\n";
			else
				print ">>>> ENGLISH NAME UNSET! <<<<\n";
			foreach ($langkeys as $langkey)
				print "        $langkey: ".$langs[$langkey]."\n";
		}
		foreach ($dict as $internal_id => $langs)
		{
			print "<attr okapi_attr_id=\"TODO\">\n";
			print "\t<groundspeak id=\"TODO\" inc=\"TODO\" name=\"TODO\" />\n";
			print "\t<opencaching site_url=\"SITEURLTODO\" id=\"$internal_id\" />\n";
			$langkeys = array_keys($langs);
			usort($langkeys, function($a, $b) {
				return ($a == "en") ? -1 : (($a == $b) ? 0 : (($a < $b) ? -1 : 1));
			});
			foreach ($langkeys as $langkey)
				print "\t<name lang=\"$langkey\">".$langs[$langkey]."</name>\n";
			print "</attr>\n";
		}

		$response = new OkapiHttpResponse();
		$response->content_type = "text/plain; charset=utf-8";
		$response->body = ob_get_clean();
		return $response;
	}

	/**
	 * Get an array of all site-specific cache-types (id => name in English).
	 */
	private static function get_all_cachetypes()
	{
		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL branch does not store cache types in many languages (just two).

			$rs = Db::query("select id, en from cache_type order by id");
		}
		else
		{
			# OCDE branch uses translation tables.

			$rs = Db::query("
				select
					ct.id,
					stt.text as en
				from
					cache_type ct
					left join sys_trans_text stt
						on ct.trans_id = stt.trans_id
						and stt.lang = 'EN'
				order by ct.id
			");
		}

		$dict = array();
		while ($row = mysql_fetch_assoc($rs)) {
			$dict[$row['id']] = $row['en'];
		}
		return $dict;
	}

	/**
	 * Get an array of all site-specific log-types (id => name in English).
	 */
	private static function get_all_logtypes()
	{
		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL branch does not store cache types in many languages (just two).

			$rs = Db::query("select id, en from log_types order by id");
		}
		else
		{
			# OCDE branch uses translation tables.

			$rs = Db::query("
				select
					lt.id,
					stt.text as en
				from
					log_types lt
					left join sys_trans_text stt
						on lt.trans_id = stt.trans_id
						and stt.lang = 'EN'
				order by lt.id
			");
		}

		$dict = array();
		while ($row = mysql_fetch_assoc($rs)) {
			$dict[$row['id']] = $row['en'];
		}
		return $dict;
	}
}
