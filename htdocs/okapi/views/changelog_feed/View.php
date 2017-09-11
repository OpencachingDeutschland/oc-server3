<?php

namespace okapi\views\changelog_feed;

use okapi\core\Response\OkapiHttpResponse;
use okapi\Settings;
use okapi\views\changelog\ChangelogHelper;

class View
{
    public static function call()
    {
        $changelog = new ChangelogHelper();
        $changes = array_merge($changelog->unavailable_changes, $changelog->available_changes);
        $changes = array_slice($changes, 0, 20);

        $vars = array(
            'changes' => $changes,
            'site_url' => Settings::get('SITE_URL'),
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "application/rss+xml; charset=utf-8";
        ob_start();
        require_once __DIR__ . '/changelog_feed.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
