<?php

namespace okapi\views\apps\index;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\OkapiHttpRequest;
use okapi\OkapiRedirectResponse;
use okapi\Settings;
use okapi\OCSession;

class View
{
    public static function call()
    {
        $langpref = isset($_GET['langpref']) ? $_GET['langpref'] : Settings::get('SITELANG');
        $langprefs = explode("|", $langpref);

        # Determine which user is logged in to OC.

        require_once($GLOBALS['rootpath']."okapi/lib/oc_session.php");
        $OC_user_id = OCSession::get_user_id();

        if ($OC_user_id == null)
        {
            $after_login = "okapi/apps/".(($langpref != Settings::get('SITELANG'))?"?langpref=".$langpref:"");
            $login_url = Settings::get('SITE_URL')."login.php?target=".urlencode($after_login);
            return new OkapiRedirectResponse($login_url);
        }

        # Get the list of authorized apps.

        $rs = Db::query("
            select c.`key`, c.name, c.url
            from
                okapi_consumers c,
                okapi_authorizations a
            where
                a.user_id = '".mysql_real_escape_string($OC_user_id)."'
                and c.`key` = a.consumer_key
            order by c.name
        ");
        $vars = array();
        $vars['okapi_base_url'] = Settings::get('SITE_URL')."okapi/";
        $vars['site_url'] = Settings::get('SITE_URL');
        $vars['site_name'] = Okapi::get_normalized_site_name();
        $vars['site_logo'] = Settings::get('SITE_LOGO');
        $vars['apps'] = array();
        while ($row = mysql_fetch_assoc($rs))
            $vars['apps'][] = $row;
        mysql_free_result($rs);

        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        Okapi::gettext_domain_init($langprefs);
        include 'index.tpl.php';
        $response->body = ob_get_clean();
        Okapi::gettext_domain_restore();
        return $response;
    }
}
