<?php

namespace okapi\views\menu;

use Exception;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\OkapiInternalConsumer;
use okapi\Settings;

require_once($GLOBALS['rootpath'].'okapi/service_runner.php');

class OkapiMenu
{
    private static function link($current_path, $link_path, $link_name)
    {
        return "<a href='".Settings::get('SITE_URL')."okapi/$link_path'".(($current_path == $link_path)
            ? " class='selected'" : "").">$link_name</a><br>";
    }

    /** Get HTML-formatted side menu representation. */
    public static function get_menu_html($current_path = null)
    {
        $chunks = array();
        if (Okapi::$revision)
            $chunks[] = "<div class='revision'>rev. ".Okapi::$revision."</div>";
        $chunks[] = "<div class='main'>";
        $chunks[] = self::link($current_path, "introduction.html", "Introduction");
        $chunks[] = self::link($current_path, "signup.html", "Sign up");
        $chunks[] = self::link($current_path, "examples.html", "Examples");
        $chunks[] = "</div>";

        # We need a list of all methods. We do not need their descriptions, so
        # we won't use the apiref/method_index method to get it, the static list
        # within OkapiServiceRunner will do.

        $methodnames = OkapiServiceRunner::$all_names;
        sort($methodnames);

        # We'll break them up into modules, for readability.

        $module_methods = array();
        foreach ($methodnames as $methodname)
        {
            $pos = strrpos($methodname, "/");
            $modulename = substr($methodname, 0, $pos);
            $method_short_name = substr($methodname, $pos + 1);
            if (!isset($module_methods[$modulename]))
                $module_methods[$modulename] = array();
            $module_methods[$modulename][] = $method_short_name;
        }
        $modulenames = array_keys($module_methods);
        sort($modulenames);

        foreach ($modulenames as $modulename)
        {
            $chunks[] = "<div class='module'>$modulename</div>";
            $chunks[] = "<div class='methods'>";
            foreach ($module_methods[$modulename] as $method_short_name)
                $chunks[] = self::link($current_path, "$modulename/$method_short_name.html", "$method_short_name");
            $chunks[] = "</div>";
        }
        return implode("", $chunks);
    }

    public static function get_installations()
    {
        $installations = OkapiServiceRunner::call("services/apisrv/installations",
            new OkapiInternalRequest(new OkapiInternalConsumer(), null, array()));
        foreach ($installations as &$inst_ref)
            $inst_ref['selected'] = ($inst_ref['site_url'] == Settings::get('SITE_URL'));
        return $installations;
    }
}
