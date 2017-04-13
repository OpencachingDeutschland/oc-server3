<?php

namespace okapi\services\apiref\issue;

use ErrorException;
use okapi\BadRequest;
use okapi\Cache;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\Settings;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 0
        );
    }

    public static function call(OkapiRequest $request)
    {
        $issue_id = $request->get_parameter('issue_id');
        if (!$issue_id)
            throw new ParamMissing('issue_id');
        if ((!preg_match("/^[0-9]+$/", $issue_id)) || (strlen($issue_id) > 6))
            throw new InvalidParam('issue_id');

        $cache_key = "apiref/issue#".$issue_id;
        $result = Cache::get($cache_key);
        if ($result == null)
        {
            # Download the number of comments from GitHub Issue Tracker.

            try
            {
                $extra_headers = array();
                $extra_headers[] = "Accept: application/vnd.github.v3.html+json";
                $extra_headers[] = "User-Agent: https://github.com/opencaching/okapi/";
                if (Settings::get('GITHUB_ACCESS_TOKEN')) {
                    $extra_headers[] = "Authorization: token ".Settings::get('GITHUB_ACCESS_TOKEN');
                }
                $opts = array(
                    'http' => array(
                        'method' => "GET",
                        'timeout' => 2.0,
                        'header' => implode("\r\n", $extra_headers),
                    )
                );
                $context = stream_context_create($opts);
                $json = file_get_contents("https://api.github.com/repos/opencaching/okapi/issues/$issue_id",
                    false, $context);
            }
            catch (ErrorException $e)
            {
                throw new BadRequest("Sorry, we could not retrieve issue stats from the GitHub site. ".
                    "This is probably due to a temporary connection problem. Try again later or contact ".
                    "us if this seems permanent.");
            }

            $doc = json_decode($json, true);
            $result = array(
                'id' => $issue_id + 0,
                'last_updated' => $doc['updated_at'],
                'title' => $doc['title'],
                'url' => $doc['html_url'],
                'comment_count' => $doc['comments']
            );

            # On one hand, we want newly added comments to show up quickly.
            # On the other, we don't want OKAPI to spam GitHub with queries.
            # So it's difficult to choose the best timeout for this.

            Cache::set($cache_key, $result, 3600);
        }
        return Okapi::formatted_response($request, $result);
    }
}
