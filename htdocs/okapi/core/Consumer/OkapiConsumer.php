<?php

namespace okapi\core\Consumer;

#
# Including OAuth internals. Preparing OKAPI Consumer and Token classes.
#

use okapi\core\OAuth\OAuthConsumer;

class OkapiConsumer extends OAuthConsumer
{
    public $name;
    public $url;
    public $email;

    /**
     * A set of binary flags indicating "special permissions".
     *
     * Some chosen Consumers gain special permissions within OKAPI. These
     * permissions are set by direct SQL UPDATEs in the database, and are not
     * part of the official documentation, nor are they backward-compatible.
     *
     * Before you grant any of these permissions to any Consumer, make him
     * aware, that he may loose them at any time (e.g. after OKAPI update)!
     */
    private $bflags;

    /**
     * Allows the consumer to set higher values on the "limit" parameters of
     * some methods.
     */
    const FLAG_SKIP_LIMITS = 1;

    /**
     * Allows the consumer to call the "services/caches/map/tile" method.
     */
    const FLAG_MAPTILE_ACCESS = 2;

    /**
     * Marks the consumer key as 'revoked', i.e. disables the consumer.
     */
    const FLAG_KEY_REVOKED = 4;

    public function __construct($key, $secret, $name, $url, $email, $bflags=0)
    {
        parent::__construct($key, $secret, null);
        $this->name = $name;
        $this->url = $url;
        $this->email = $email;
        $this->bflags = $bflags;
    }

    /**
     * Returns true if the consumer has the given flag set. See class contants
     * for the list of available flags.
     */
    public function hasFlag($flag)
    {
        return ($this->bflags & $flag) > 0;
    }

    public function __toString()
    {
        return "OkapiConsumer[key=$this->key,name=$this->name]";
    }
}
