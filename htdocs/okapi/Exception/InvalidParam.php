<?php

namespace okapi\Exception;

/** Common type of BadRequest: Parameter has invalid value. */
class InvalidParam extends BadRequest
{
    public $paramName;

    /** What was wrong about the param? */
    public $whats_wrong_about_it;

    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'invalid_parameter';
        $extras['parameter'] = $this->paramName;
        $extras['whats_wrong_about_it'] = $this->whats_wrong_about_it;
    }
    public function __construct($paramName, $whats_wrong_about_it = "", $code = 0)
    {
        $this->paramName = $paramName;
        $this->whats_wrong_about_it = $whats_wrong_about_it;
        if ($whats_wrong_about_it)
            parent::__construct("Parameter '$paramName' has invalid value: ".$whats_wrong_about_it, $code);
        else
            parent::__construct("Parameter '$paramName' has invalid value.", $code);
    }
}
