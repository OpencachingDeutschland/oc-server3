<?php

namespace okapi\Exception;

/** Client didn't provide one of the key OAuth parameters. */
class OAuthMissingParameterException extends OAuthServer400Exception {
    protected $param_name;
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'missing_parameter';
        $extras['parameter'] = $this->param_name;
    }
    public function __construct($param_name) {
        parent::__construct("Missing '$param_name' parameter. This parameter is required.");
        $this->param_name = $param_name;
    }
    public function getParamName() { return $this->param_name; }
}
