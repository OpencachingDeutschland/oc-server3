<?php

namespace okapi\Exception;

/** "Lock timeout exceeded; try restarting transaction" error. */
class DbLockWaitTimeoutException extends DbException {}
