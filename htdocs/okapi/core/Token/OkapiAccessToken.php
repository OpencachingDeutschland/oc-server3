<?php

namespace okapi\core\Token;

class OkapiAccessToken extends OkapiToken
{
    public $user_id;

    public function __construct($key, $secret, $consumer_key, $user_id)
    {
        parent::__construct($key, $secret, $consumer_key, 'access');
        $this->user_id = $user_id;
    }
}
