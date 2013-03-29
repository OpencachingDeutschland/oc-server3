<?php

namespace okapi\services\attrs;

use Exception;
use ErrorException;
use okapi\Okapi;
use okapi\Settings;
use okapi\Cache;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use SimpleXMLElement;


class AttrHelper
{
	private static $CACHE_KEY = 'attrs/attrlist/1';
	private static $attr_dict = null;
	private static $last_refreshed = null;

	/**
	 * Forces the download of the new attributes from Google Code.
	 */
	private static function refresh_now()
	{
		try
		{
			$opts = array(
				'http' => array(
					'method' => "GET",
					'timeout' => 5.0
				)
			);
			$context = stream_context_create($opts);
			$xml = file_get_contents("http://opencaching-api.googlecode.com/svn/trunk/etc/attributes.xml",
				false, $context);
		}
		catch (ErrorException $e)
		{
			# Google failed on us. We won't update the cached attributes.
			return;
		}

		$my_site_url = "http://opencaching.pl/"; // WRTODO
		$doc = simplexml_load_string($xml);
		$cachedvalue = array(
			'attr_dict' => array(),
			'last_refreshed' => time(),
		);
		foreach ($doc->attr as $attrnode)
		{
			$attr = array(
				'code' => (string)$attrnode['okapi_attr_id'],
				'gs_equiv' => null,
				'internal_id' => null,
				'names' => array(),
				'descriptions' => array()
			);
			foreach ($attrnode->groundspeak as $gsnode)
			{
				$attr['gs_equiv'] = array(
					'id' => (int)$gsnode['id'],
					'inc' => in_array((string)$gsnode['inc'], array("true", "1")) ? 1 : 0,
					'name' => (string)$gsnode['name']
				);
			}
			foreach ($attrnode->opencaching as $ocnode)
			{
				if ((string)$ocnode['site_url'] == $my_site_url) {
					$attr['internal_id'] = (int)$ocnode['id'];
				}
			}
			foreach ($attrnode->name as $namenode)
			{
				$attr['names'][(string)$namenode['lang']] = (string)$namenode;
			}
			foreach ($attrnode->desc as $descnode)
			{
				$xml = $descnode->asxml(); /* contains "<desc lang="...">" and "</desc>" */
				$innerxml = preg_replace("/(^[^>]+>)|(<[^<]+$)/us", "", $xml);
				$attr['descriptions'][(string)$descnode['lang']] = self::cleanup_string($innerxml);
			}
			$cachedvalue['attr_dict'][$attr['code']] = $attr;
		}

		# Cache it for a month (just in case, usually it will be refreshed every day).

		Cache::set(self::$CACHE_KEY, $cachedvalue, 30*86400);
		self::$attr_dict = $cachedvalue['attr_dict'];
		self::$last_refreshed = $cachedvalue['last_refreshed'];
	}

	/**
	 * Initialize all the internal attributes (if not yet initialized). This
	 * loads attribute values from the cache. If they are not present in the cache,
	 * it won't download them from Google Code, it will initialize them as empty!
	 */
	private static function init_from_cache()
	{
		if (self::$attr_dict !== null)
		{
			/* Already initialized. */
			return;
		}
		$cachedvalue = Cache::get(self::$CACHE_KEY);
		if ($cachedvalue === null)
		{
			$cachedvalue = array(
				'attr_dict' => array(),
				'last_refreshed' => 0,
			);
		}
		self::$attr_dict = $cachedvalue['attr_dict'];
		self::$last_refreshed = $cachedvalue['last_refreshed'];
	}

	/**
	 * Check if the cached attribute values might be stale. If they were not
	 * refreshed in a while, perform the refresh from Google Code. (This might
	 * take a couple of seconds, it should be done via a cronjob.)
	 */
	public static function refresh_if_stale()
	{
		self::init_from_cache();
		if (self::$last_refreshed < time() - 86400)
			self::refresh_now();
		if (self::$last_refreshed < time() - 3 * 86400)
		{
			Okapi::mail_admins(
				"OKAPI was unable to refresh attributes",
				"OKAPI periodically refreshes all cache attributes from the list\n".
				"kept in global repository. OKAPI tried to contact the repository,\n".
				"but it failed. Your list of attributes might be stale.\n\n".
				"You should probably update OKAPI or contact OKAPI developers."
			);
		}
	}

	/**
	 * Return a dictionary of all attributes. The format is the same as in the "attributes"
	 * key returned by the "services/attrs/attrlist" method.
	 */
	public static function get_attrdict()
	{
		self::init_from_cache();
		return self::$attr_dict;
	}

	/** "\n\t\tBla   blabla\n\t\t<b>bla</b>bla.\n\t" => "Bla blabla <b>bla</b>bla." */
	private static function cleanup_string($s)
	{
		return preg_replace('/(^\s+)|(\s+$)/us', "", preg_replace('/\s+/us', " ", $s));
	}
}
