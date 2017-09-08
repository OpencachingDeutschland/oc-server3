<?php

namespace okapi\core\Exception;

/** Thrown on PHP's FATAL errors (detected in a shutdown function). */
class FatalError extends \ErrorException {}
