<?php

namespace okapi\Exception;

/** Common type of BadRequest: Required parameter is missing. */
class ParamMissing extends BadRequest
{
    private $paramName;
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'missing_parameter';
        $extras['parameter'] = $this->paramName;
    }
    public function __construct($paramName)
    {
        parent::__construct("Required parameter '$paramName' is missing.");
        $this->paramName = $paramName;
    }
}
