<?php

namespace okapi\core\Exception;

/** Client used expired timestamp (or timestamp too far in future). */
class OAuthExpiredTimestampException extends OAuthServer400Exception {
    protected $usersTimestamp;
    protected $ourTimestamp;
    protected $threshold;
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'invalid_timestamp';
        $extras['yours'] = $this->usersTimestamp;
        $extras['ours'] = $this->ourTimestamp;
        $extras['difference'] = $this->ourTimestamp - $this->usersTimestamp;
        $extras['threshold'] = $this->threshold;
    }
    public function __construct($users, $ours, $threshold) {
        $this->usersTimestamp = $users;
        $this->ourTimestamp = $ours;
        $this->threshold = $threshold;
        parent::__construct("Expired timestamp, yours $this->usersTimestamp, ours $this->ourTimestamp (threshold $this->threshold).");
    }
    public function getUsersTimestamp() { return $this->usersTimestamp; }
    public function getOurTimestamp() { return $this->ourTimestamp; }
}
