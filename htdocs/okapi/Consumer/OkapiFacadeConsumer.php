<?php

namespace okapi\Consumer;

/**
 * Used by calls made via Facade class. SHOULD NOT be referenced anywhere else from
 * within OKAPI code.
 */
class OkapiFacadeConsumer extends OkapiConsumer
{
    public function __construct()
    {
        $admins = \get_admin_emails();
        parent::__construct('facade', null, "Internal usage via Facade", null, $admins[0]);
    }
}
