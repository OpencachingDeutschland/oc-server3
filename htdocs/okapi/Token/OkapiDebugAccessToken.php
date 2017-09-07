<?php

namespace okapi\Token;

/** Used when debugging with DEBUG_AS_USERNAME. */
class OkapiDebugAccessToken extends OkapiAccessToken
{
    public function __construct($user_id)
    {
        parent::__construct('debug-'.$user_id, null, 'debug', $user_id);
    }
}
