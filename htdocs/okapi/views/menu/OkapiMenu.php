<?php

namespace okapi\views\menu;

use okapi\Consumer\OkapiInternalConsumer;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;
use okapi\Settings;

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
        if (Okapi::$version_number)
            $chunks[] = "<div class='revision'>ver. ".Okapi::$version_number.
                " (".substr(Okapi::$git_revision, 0, 7).")</div>";
        $chunks[] = "<div class='main'>";
        $chunks[] = self::link($current_path, "introduction.html", "Introduction");
        $chunks[] = self::link($current_path, "signup.html", "Sign up");
        $chunks[] = self::link($current_path, "examples.html", "Examples");
        $chunks[] = self::link($current_path, "changelog.html", "Changelog");
        $chunks[] = "</div>";

        # Retrieve the index of all methods. Note, that services/apiref/method_index
        # method caches its results. This may result in delayed propagation of changes
        # in development environments.

        $method_index_result = OkapiServiceRunner::call(
            "services/apiref/method_index",
            new OkapiInternalRequest(new OkapiInternalConsumer(), null, array())
        );
        $method_descs = array();
        foreach ($method_index_result as &$method_desc_ref) {
            $method_descs[$method_desc_ref['name']] = &$method_desc_ref;
        }

        # We'll break them up into modules, for readability.

        $methodnames = OkapiServiceRunner::$all_names;
        sort($methodnames);

        $module_methods = array();
        foreach ($methodnames as $methodname)
        {
            $pos = strrpos($methodname, "/");
            $modulename = substr($methodname, 0, $pos);
            if (!isset($module_methods[$modulename]))
                $module_methods[$modulename] = array();
            $module_methods[$modulename][] = $method_descs[$methodname];
        }
        $modulenames = array_keys($module_methods);
        sort($modulenames);

        foreach ($modulenames as $modulename)
        {
            $chunks[] = "<div class='module'>$modulename</div>";
            $chunks[] = "<div class='methods'>";
            foreach ($module_methods[$modulename] as $method_desc) {
                $method_short_name = $method_desc['short_name'];
                $chunks[] = Okapi::format_infotags($method_desc['infotags']);
                $chunks[] = self::link($current_path, "$modulename/$method_short_name.html", "$method_short_name");
            }
            $chunks[] = "</div>";
        }
        return implode("", $chunks);
    }

    public static function get_installations()
    {
        $installations = OkapiServiceRunner::call("services/apisrv/installations",
            new OkapiInternalRequest(new OkapiInternalConsumer(), null, array()));
        $site_url = Settings::get('SITE_URL');

        foreach ($installations as &$inst_ref)
        {
            # $inst_ref['site_url'] and $site_url can have different protocols
            # (http / https). We compare only the domain parts and use
            # $site_url (which has the current request's protocol) for the menu
            # so that the menu works properly.

            if (self::domains_are_equal($inst_ref['site_url'], $site_url))
            {
                $inst_ref['site_url'] = $site_url;
                $inst_ref['okapi_base_url'] = $site_url . 'okapi/';
                $inst_ref['selected'] = true;
            }
            else
            {
                $inst_ref['selected'] = false;
            }
        }
        return $installations;
    }

    private static function domains_are_equal($url1, $url2)
    {
        $domain1 = parse_url($url1, PHP_URL_HOST);
        $domain2 = parse_url($url2, PHP_URL_HOST);
        return $domain1 == $domain2;
    }
}
