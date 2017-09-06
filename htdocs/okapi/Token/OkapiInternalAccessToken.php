<?php

namespace okapi\Token;

/** Use this in conjunction with OkapiInternalConsumer. */
class OkapiInternalAccessToken extends OkapiAccessToken
{
    public function __construct($user_id)
    {
        parent::__construct('internal-'.$user_id, null, 'internal', $user_id);
    }
}
