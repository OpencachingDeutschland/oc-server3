<?php

namespace okapi\core\Exception;

/** "Lock timeout exceeded; try restarting transaction" error. */
class DbLockWaitTimeoutException extends DbException {}
