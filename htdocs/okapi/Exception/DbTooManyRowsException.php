<?php

namespace okapi\Exception;

/** Thrown when select_value or select_row get too many rows. */
class DbTooManyRowsException extends DbException {}
