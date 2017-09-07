<?php

namespace okapi\Token;

use okapi\OAuth\OAuthToken;

class OkapiToken extends OAuthToken
{
    public $consumer_key;
    public $token_type;

    public function __construct($key, $secret, $consumer_key, $token_type)
    {
        parent::__construct($key, $secret);
        $this->consumer_key = $consumer_key;
        $this->token_type = $token_type;
    }
}
