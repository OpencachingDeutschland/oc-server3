<?php
namespace okapi\Core\Exception;

/**
 * Thrown in CronJobController::run_jobs when other thread is already
 * handling the jobs.
 */
class JobsAlreadyInProgress extends \Exception {}
