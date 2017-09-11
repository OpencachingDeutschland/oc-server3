<?php

namespace okapi\core\Request;

/**
 * Represents an OKAPI web method request.
 *
 * Use this class to get parameters from your request and access
 * Consumer and Token objects. Please note, that request method
 * SHOULD be irrelevant to you: GETs and POSTs are interchangable
 * within OKAPI, and it's up to the caller which one to choose.
 * If you think using GET is "unsafe", then probably you forgot to
 * add OAuth signature requirement (consumer=required) - this way,
 * all the "unsafety" issues of using GET vanish.
 */
abstract class OkapiRequest
{
    public $consumer;
    public $token;
    public $etag;  # see: https://en.wikipedia.org/wiki/HTTP_ETag

    /**
     * Set this to true, for some method to allow you to set higher "limit"
     * parameter than usually allowed. This should be used ONLY by trusted,
     * fast and *cacheable* code!
     */
    public $skip_limits = false;

    /**
     * Return request parameter, or NULL when not found. Use this instead of
     * $_GET or $_POST or $_REQUEST.
     */
    public abstract function get_parameter($name);

    /**
     * Return the list of all request parameters. You should use this method
     * ONLY when you use <import-params/> in your documentation and you want
     * to pass all unknown parameters onto the other method.
     */
    public abstract function get_all_parameters_including_unknown();

    /** Return true, if this requests is to be logged as HTTP request in okapi_stats. */
    public abstract function is_http_request();
}
