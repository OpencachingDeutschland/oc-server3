<?php

#
# All HTTP requests within the /okapi/ path are redirected through this
# controller. From here we'll pass them to the right entry point (or
# display an appropriate error message).
#
# To learn more about OKAPI, see core.php.
#

# -------------------------

#
# Set up the rootpath. If OKAPI is called via its Facade entrypoint, then this
# variable is being set up by the OC site. If it is called via the controller
# endpoint (this one!), then we need to set it up ourselves.
#

namespace okapi;

use okapi\core\Exception\OkapiExceptionHandler;
use okapi\core\Okapi;
use okapi\core\OkapiErrorHandler;

$GLOBALS['rootpath'] = __DIR__.'/../';

require_once __DIR__ . '/autoload.php';

OkapiErrorHandler::$treat_notices_as_errors = true;

if (ob_list_handlers() === ['default output handler']) {
    # We will assume that this one comes from "output_buffering" being turned on
    # in PHP config. This is very common and probably is good for most other OC
    # pages. But we don't need it in OKAPI. We will just turn this off.
    ob_end_clean();
}

# Setting handlers. Errors will now throw exceptions, and all exceptions
# will be properly handled. (Unfortunately, only SOME errors can be caught
# this way, PHP limitations...)

set_exception_handler(array(OkapiExceptionHandler::class, 'handle'));
set_error_handler(array(OkapiErrorHandler::class, 'handle'));
register_shutdown_function(array(OkapiErrorHandler::class, 'handle_shutdown'));

Okapi::gettext_domain_init();
OkapiScriptEntryPointController::dispatch_request($_SERVER['REQUEST_URI']);
Okapi::gettext_domain_restore();
