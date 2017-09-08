<?php

namespace okapi\core\Request;

class OkapiInternalRequest extends OkapiRequest
{
    private $parameters;

    /**
     * Set this to true, if you want this request to be considered as HTTP request
     * in okapi_stats tables. This is useful when running requests through Facade
     * (we want them logged and displayed in weekly report).
     */
    public $perceive_as_http_request = false;

    /**
     * By default, OkapiInsernalRequests work differently than OkapiRequests -
     * they TRY to return PHP objects (like arrays), instead of OkapiResponse
     * objects. Set this to true, if you want this request to work as a regular
     * one - and receive OkapiResponse instead of the PHP object.
     */
    public $i_want_OkapiResponse = false;

    /**
     * You may use "null" values in parameters if you want them skipped
     * (null-ized keys will be removed from parameters).
     */
    public function __construct($consumer, $token, $parameters)
    {
        $this->consumer = $consumer;
        $this->token = $token;
        $this->parameters = array();
        foreach ($parameters as $key => $value)
            if ($value !== null)
                $this->parameters[$key] = $value;
    }

    public function get_parameter($name)
    {
        if (isset($this->parameters[$name]))
            return $this->parameters[$name];
        else
            return null;
    }

    public function get_all_parameters_including_unknown()
    {
        return $this->parameters;
    }

    public function is_http_request() { return $this->perceive_as_http_request; }
}
