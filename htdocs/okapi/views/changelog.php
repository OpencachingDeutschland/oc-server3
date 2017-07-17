<?php

namespace okapi\views\changelog;

use okapi\Okapi;
use okapi\OkapiHttpResponse;
use okapi\Settings;
use okapi\views\menu\OkapiMenu;

class View
{
    public static function call()
    {
        require_once 'okapi/views/menu.inc.php';
        require_once 'okapi/views/changelog_helper.inc.php';

        $changelog = new Changelog();

        $vars = array(
            'menu' => OkapiMenu::get_menu_html("changelog.html"),
            'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
            'site_url' => Settings::get('SITE_URL'),
            'installations' => OkapiMenu::get_installations(),
            'okapi_rev' => Okapi::$version_number,
            'site_name' => Okapi::get_normalized_site_name(),
            'changes' => array(
                'unavailable' => $changelog->unavailable_changes,
                'available' => $changelog->available_changes
            ),
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        include 'changelog.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
