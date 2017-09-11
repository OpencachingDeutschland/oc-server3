<?php
namespace okapi\core\Exception;

/**
 * Thrown in CronJobController::run_jobs when other thread is already
 * handling the jobs.
 */
class JobsAlreadyInProgress extends \Exception {}
