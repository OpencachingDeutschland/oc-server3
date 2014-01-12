<?php

namespace okapi\views\devel\clogentry;

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
		if (!isset($_GET['id'])) {
			throw new ParamMissing("id");
		}
		$tmp = Db::select_value("
			select data
			from okapi_clog
			where id='".mysql_real_escape_string($_GET['id'])."'
		");
		$data = unserialize(gzinflate($tmp));

		$response = new OkapiHttpResponse();
		$response->content_type = "application/json; charset=utf-8";
		$response->body = json_encode($data);
		return $response;
	}
}
