<?php

namespace okapi\core\Consumer;

/**
 * Use this when calling OKAPI methods internally from OKAPI code. (If you want call
 * OKAPI from other OC code, you must use Facade class - see Facade.php)
 */
class OkapiInternalConsumer extends OkapiConsumer
{
    public function __construct()
    {
        $admins = \get_admin_emails();
        parent::__construct('internal', null, "Internal OKAPI jobs", null, $admins[0]);
    }
}
