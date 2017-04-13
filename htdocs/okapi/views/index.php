<?php

namespace okapi\views\index;

use okapi\OkapiRedirectResponse;
use okapi\Settings;

class View
{
    public static function call()
    {
        # This is called when someone displays "http://../okapi/" (with no
        # html path at the end). We will redirect to the introduction page.

        return new OkapiRedirectResponse(Settings::get('SITE_URL').
            "okapi/introduction.html");
    }
}
