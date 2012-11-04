<?php

namespace okapi\views\attrlist;

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

class View
{
	public static function call()
	{
		# This is a hidden page for OKAPI developers. It will list all
		# attributes defined in this OC installation.
		
		$rs = Db::query("select id, language, text_long from cache_attrib order by id");
		$dict = array();
		while ($row = mysql_fetch_assoc($rs))
			$dict[$row['id']][strtolower($row['language'])] = $row['text_long'];
		$chunks = array();
		foreach ($dict as $internal_id => $langs)
		{
			$chunks[] = "<attr code='...' internal_id='$internal_id'";
			$langkeys = array_keys($langs);
			sort($langkeys);
			foreach ($langkeys as $langkey)
				$chunks[] = " $langkey='".$langs[$langkey]."'";
			$chunks[] = " />\n";
		}
		
		$response = new OkapiHttpResponse();
		$response->content_type = "text/plain; charset=utf-8";
		$response->body = implode("", $chunks);
		return $response;
	}

}
