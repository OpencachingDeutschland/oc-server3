<?php

namespace okapi\views\apps\authorized;

use okapi\core\Db;
use okapi\core\Okapi;
use okapi\core\Response\OkapiHttpResponse;
use okapi\core\Response\OkapiRedirectResponse;
use okapi\Settings;

class View
{
    public static function call()
    {
        $token_key = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : '';
        $verifier = isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : '';
        $langpref = isset($_GET['langpref']) ? $_GET['langpref'] : Settings::get('SITELANG');
        $langprefs = explode("|", $langpref);

        $token = Db::select_row("
            select
                c.`key` as consumer_key,
                c.name as consumer_name,
                c.url as consumer_url,
                t.verifier
            from
                okapi_consumers c,
                okapi_tokens t
            where
                t.`key` = '".Db::escape_string($token_key)."'
                and t.consumer_key = c.`key`
        ");

        if (!$token)
        {
            # Probably Request Token has expired or it was already used. We'll
            # just redirect to the Opencaching main page.
            return new OkapiRedirectResponse(Settings::get('SITE_URL'));
        }

        $vars = array(
            'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
            'token' => $token,
            'verifier' => $verifier,
            'site_name' => Okapi::get_normalized_site_name(),
            'site_url' => Settings::get('SITE_URL'),
            'site_logo' => Settings::get('SITE_LOGO'),
        );
        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        Okapi::gettext_domain_init($langprefs);
        include __DIR__ . '/authorized.tpl.php';
        $response->body = ob_get_clean();
        Okapi::gettext_domain_restore();
        return $response;
    }
}
