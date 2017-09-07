<?php

namespace okapi\Token;

/** Use this in conjunction with OkapiFacadeConsumer. */
class OkapiFacadeAccessToken extends OkapiAccessToken
{
    public function __construct($user_id)
    {
        parent::__construct('facade-'.$user_id, null, 'facade', $user_id);
    }
}
