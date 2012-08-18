<?php

namespace okapi\services\caches\formatters\garmin;

use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\BadRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiAccessToken;
use okapi\services\caches\search\SearchAssistant;

use \ZipArchive;

class WebService
{
	private static $shutdown_function_registered = false;
	private static $files_to_unlink = array();
	
	public static function options()
	{
		return array(
			'min_auth_level' => 1
		);
	}
	
	public static function call(OkapiRequest $request)
	{
		$cache_codes = $request->get_parameter('cache_codes');
		if ($cache_codes === null) throw new ParamMissing('cache_codes');
		
		# Issue 106 requires us to allow empty list of cache codes to be passed into this method.
		# All of the queries below have to be ready for $cache_codes to be empty!
		
		$langpref = $request->get_parameter('langpref');
		if (!$langpref) $langpref = "en";
		$images = $request->get_parameter('images');
		if (!$images) $images = "all";
		if (!in_array($images, array("none", "all", "spoilers", "nonspoilers")))
			throw new InvalidParam('images');
		
		# Start creating ZIP archive.
		
		$tempfilename = sys_get_temp_dir()."/garmin".time().rand(100000,999999).".zip";
		$zip = new ZipArchive();
		if ($zip->open($tempfilename, ZIPARCHIVE::CREATE) !== true)
			throw new Exception("ZipArchive class could not create temp file $tempfilename. Check permissions!");
		
		# Create basic structure
		
		$zip->addEmptyDir("Garmin");
		$zip->addEmptyDir("Garmin/GPX");
		$zip->addEmptyDir("Garmin/GeocachePhotos");
		
		# Include a GPX file compatible with Garmin devices. It should include all
		# Geocaching.com (groundspeak:) and Opencaching.com (ox:) extensions. It will
		# also include image references (actual images will be added as separate files later)
		# and personal data (if the method was invoked using Level 3 Authentication).
		
		$zip->addFromString("Garmin/GPX/opencaching".time().rand(100000,999999).".gpx",
			OkapiServiceRunner::call('services/caches/formatters/gpx', new OkapiInternalRequest(
			$request->consumer, $request->token, array(
				'cache_codes' => $cache_codes,
				'langpref' => $langpref,
				'ns_ground' => 'true',
				'ns_ox' => 'true',
				'images' => 'ox:all',
				'attrs' => 'ox:tags',
				'trackables' => 'desc:count',
				'alt_wpts' => 'true',
				'recommendations' => 'desc:count',
				'latest_logs' => 'true',
				'lpc' => 'all',
				'my_notes' => ($request->token != null) ? "desc:text" : "none"
			)))->get_body());

		# Then, include all the images.
		
		$caches = OkapiServiceRunner::call('services/caches/geocaches', new OkapiInternalRequest(
			$request->consumer, $request->token, array('cache_codes' => $cache_codes,
			'langpref' => $langpref, 'fields' => "images")));
		if (count($caches) > 50)
			throw new InvalidParam('cache_codes', "The maximum number of caches allowed to be downloaded with this method is 50.");
		if ($images != 'none')
		{
			foreach ($caches as $cache_code => $dict)
			{
				$imgs = $dict['images'];
				if (count($imgs) == 0)
					continue;
				$dir = "Garmin/GeocachePhotos/".$cache_code[strlen($cache_code) - 1];
				$zip->addEmptyDir($dir); # fails silently if it already exists
				$dir .= "/".$cache_code[strlen($cache_code) - 2];
				$zip->addEmptyDir($dir);
				$dir .= "/".$cache_code;
				$zip->addEmptyDir($dir);
				foreach ($imgs as $no => $img)
				{
					if ($images == 'spoilers' && (!$img['is_spoiler']))
						continue;
					if ($images == 'nonspoilers' && $img['is_spoiler'])
						continue;
					if (strtolower(substr($img['url'], strlen($img['url']) - 4)) != ".jpg")
						continue;
					if ($img['is_spoiler']) {
						$zip->addEmptyDir($dir."/Spoilers");
						$zippath = $dir."/Spoilers/".$img['unique_caption'].".jpg";
					} else {
						$zippath = $dir."/".$img['unique_caption'].".jpg";
					}
					
					# The safest way would be to use the URL, but that would be painfully slow!
					# That's why we're trying to access files directly (and fail silently on error).
					# This was tested on OCPL server only.
					
					# Note: Oliver Dietz (oc.de) replied that images with 'local' set to 0 could not
					# be accessed locally. But all the files have 'local' set to 1 anyway.
					
					$syspath = $GLOBALS['picdir']."/".$img['uuid'].".jpg";
					if (!file_exists($syspath))
						continue;
					$file = file_get_contents($syspath);
					if ($file)
						$zip->addFromString($zippath, $file);
				}
			}
		}
		
		$zip->close();
		
		# The result could be big. Bigger than our memory limit. We will
		# return an open file stream instead of a string. We also should
		# set a higher time limit, because downloading this response may
		# take some time over slow network connections (and I'm not sure
		# what is the PHP's default way of handling such scenario).
		
		set_time_limit(600);
		$response = new OkapiHttpResponse();
		$response->content_type = "application/zip";
		$response->content_disposition = 'Content-Disposition: attachment; filename="results.zip"';
		$response->stream_length = filesize($tempfilename);
		$response->body = fopen($tempfilename, "rb");
		$response->allow_gzip = false;
		self::add_file_to_unlink($tempfilename);
		return $response;
	}
	
	private static function add_file_to_unlink($filename)
	{
		if (!self::$shutdown_function_registered)
			register_shutdown_function(array("okapi\\services\\caches\\formatters\\garmin\\WebService", "unlink_temporary_files"));
		self::$files_to_unlink[] = $filename;
	}
	
	public static function unlink_temporary_files()
	{
		foreach (self::$files_to_unlink as $filename)
			@unlink($filename);
		self::$files_to_unlink = array();
	}
}
