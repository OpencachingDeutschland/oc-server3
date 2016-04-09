<?php

namespace okapi\views\changelog_feed;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\views\changelog\Changelog;


class View
{
    public static function call()
    {
        require_once($GLOBALS['rootpath'].'okapi/views/changelog_helper.inc.php');

        $changelog = new Changelog();
        $changes = array_merge($changelog->unavailable_changes, $changelog->available_changes);
        $changes = array_slice($changes, 0, 20);

        $vars = array(
            'changes' => $changes,
            'site_url' => Settings::get('SITE_URL'),
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "application/rss+xml; charset=utf-8";
        ob_start();
        include 'changelog_feed.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
