<?php

namespace okapi\services\oauth\authorize;

use okapi\InvalidParam;
use okapi\OkapiRedirectResponse;
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
        $token_key = $request->get_parameter('oauth_token');
        if (!$token_key)
            throw new ParamMissing("oauth_token");
        $langpref = $request->get_parameter('langpref');
        $interactivity = $request->get_parameter('interactivity');
        if (!$interactivity) $interactivity = 'minimal';
        if (!in_array($interactivity, array('minimal', 'confirm_user')))
            throw new InvalidParam('interactivity', $interactivity);

        # Redirect to the "apps" folder. This is done there (not here)
        # because: 1) we don't want any cookie or session-handling
        # done in the "services" folder. 2) "services" don't display
        # any interactive webpages, they just return the result.

        return new OkapiRedirectResponse(Settings::get('SITE_URL')."okapi/apps/authorize".
            "?oauth_token=".$token_key.(($langpref != null) ? "&langpref=".$langpref : "").
            "&interactivity=".$interactivity);
    }
}
