<?php

namespace okapi\views\apps\revoke_access;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\OkapiHttpRequest;
use okapi\OkapiRedirectResponse;
use okapi\OCSession;

class View
{
    public static function call()
    {
        # Determine which user is logged in to OC.

        require_once($GLOBALS['rootpath']."okapi/lib/oc_session.php");
        $OC_user_id = OCSession::get_user_id();

        # Ensure a user is logged in.

        if ($OC_user_id == null)
        {
            $after_login = "okapi/apps/"; # it is correct, if you're wondering
            $login_url = Settings::get('SITE_URL')."login.php?target=".urlencode($after_login);
            return new OkapiRedirectResponse($login_url);
        }

        $consumer_key = isset($_REQUEST['consumer_key']) ? $_REQUEST['consumer_key'] : '';

        # Just remove app (if it doesn't exist - nothing wrong will happen anyway).

        Db::execute("
            delete from okapi_tokens
            where
                user_id = '".mysql_real_escape_string($OC_user_id)."'
                and consumer_key = '".mysql_real_escape_string($consumer_key)."'
        ");
        Db::execute("
            delete from okapi_authorizations
            where
                user_id = '".mysql_real_escape_string($OC_user_id)."'
                and consumer_key = '".mysql_real_escape_string($consumer_key)."'
        ");

        # Redirect back to the apps page.

        return new OkapiRedirectResponse(Settings::get('SITE_URL')."okapi/apps/");
    }
}
