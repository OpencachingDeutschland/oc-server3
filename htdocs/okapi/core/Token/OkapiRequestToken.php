<?php

namespace okapi\core\Token;

class OkapiRequestToken extends OkapiToken
{
    public $callback_url;
    public $authorized_by_user_id;
    public $verifier;

    public function __construct($key, $secret, $consumer_key, $callback_url,
        $authorized_by_user_id, $verifier)
    {
        parent::__construct($key, $secret, $consumer_key, 'request');
        $this->callback_url = $callback_url;
        $this->authorized_by_user_id = $authorized_by_user_id;
        $this->verifier = $verifier;
    }
}
