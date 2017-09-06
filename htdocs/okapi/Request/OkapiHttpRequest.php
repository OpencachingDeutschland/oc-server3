<?php

namespace okapi\Request;

use okapi\Consumer\OkapiConsumer;
use okapi\Consumer\OkapiDebugConsumer;
use okapi\Db;
use okapi\Exception\BadRequest;
use okapi\Exception\InvalidParam;
use okapi\OAuth\OAuthRequest;
use okapi\Okapi;
use okapi\Settings;
use okapi\Token\OkapiDebugAccessToken;

class OkapiHttpRequest extends OkapiRequest
{
    private $request; /* @var OAuthRequest */
    private $opt_min_auth_level; # 0..3
    private $opt_token_type = 'access'; # "access" or "request"

    public function __construct($options)
    {
        Okapi::init_internals();
        $this->init_request();
        #
        # Parsing options.
        #
        $DEBUG_AS_USERNAME = null;
        foreach ($options as $key => $value)
        {
            switch ($key)
            {
                case 'min_auth_level':
                    if (!in_array($value, array(0, 1, 2, 3)))
                    {
                        throw new \Exception("'min_auth_level' option has invalid value: $value");
                    }
                    $this->opt_min_auth_level = $value;
                    break;
                case 'token_type':
                    if (!in_array($value, array("request", "access")))
                    {
                        throw new \Exception("'token_type' option has invalid value: $value");
                    }
                    $this->opt_token_type = $value;
                    break;
                case 'DEBUG_AS_USERNAME':
                    $DEBUG_AS_USERNAME = $value;
                    break;
                default:
                    throw new \Exception("Unknown option: $key");
                    break;
            }
        }
        if ($this->opt_min_auth_level === null) throw new \Exception("Required 'min_auth_level' option is missing.");

        if ($DEBUG_AS_USERNAME != null)
        {
            # Enables debugging Level 2 and Level 3 methods. Should not be committed
            # at any time! If run on production server, make it an error.

            if (!Settings::get('DEBUG'))
            {
                throw new \Exception("Attempted to use DEBUG_AS_USERNAME in ".
                    "non-debug environment. Accidental commit?");
            }

            # Lower required authentication to Level 0, to pass the checks.

            $this->opt_min_auth_level = 0;
        }

        #
        # Let's see if the request is signed. If it is, verify the signature.
        # It it's not, check if it isn't against the rules defined in the $options.
        #

        if ($this->get_parameter('oauth_signature'))
        {
            # User is using OAuth.

            # Check for duplicate keys in the parameters. (Datastore doesn't
            # do that on its own, it caused vague server errors - issue #307.)

            $this->get_parameter('oauth_consumer');
            $this->get_parameter('oauth_version');
            $this->get_parameter('oauth_token');
            $this->get_parameter('oauth_nonce');

            # Verify OAuth request.

            list($this->consumer, $this->token) = Okapi::$server->
            verify_request2($this->request, $this->opt_token_type, $this->opt_min_auth_level == 3);
            if ($this->get_parameter('consumer_key') && $this->get_parameter('consumer_key') != $this->get_parameter('oauth_consumer_key'))
                throw new BadRequest("Inproper mixing of authentication types. You used both 'consumer_key' ".
                    "and 'oauth_consumer_key' parameters (Level 1 and Level 2), but they do not match with ".
                    "each other. Were you trying to hack me? ;)");
            if ($this->opt_min_auth_level == 3 && !$this->token)
            {
                throw new BadRequest("This method requires a valid Token to be included (Level 3 ".
                    "Authentication). You didn't provide one.");
            }
        }
        else
        {
            if ($this->opt_min_auth_level >= 2)
            {
                throw new BadRequest("This method requires OAuth signature (Level ".
                    $this->opt_min_auth_level." Authentication). You didn't sign your request.");
            }
            else
            {
                $consumer_key = $this->get_parameter('consumer_key');
                if ($consumer_key)
                {
                    $this->consumer = Okapi::$data_store->lookup_consumer($consumer_key);
                    if (!$this->consumer) {
                        throw new InvalidParam('consumer_key', "Consumer does not exist.");
                    }
                }
                if (($this->opt_min_auth_level == 1) && (!$this->consumer))
                    throw new BadRequest("This method requires the 'consumer_key' argument (Level 1 ".
                        "Authentication). You didn't provide one.");
            }
        }

        if (is_object($this->consumer) && $this->consumer->hasFlag(OkapiConsumer::FLAG_KEY_REVOKED)) {
            throw new InvalidParam(
                'consumer_key',
                "Your application was denied access to the " .
                Okapi::get_normalized_site_name() . " site " .
                "(this consumer key has been revoked)."
            );
        }

        if (is_object($this->consumer) && $this->consumer->hasFlag(OkapiConsumer::FLAG_SKIP_LIMITS)) {
            $this->skip_limits = true;
        }

        #
        # Prevent developers from accessing request parameters with PHP globals.
        # Remember, that OKAPI requests can be nested within other OKAPI requests!
        # Search the code for "new OkapiInternalRequest" to see examples.
        #

        $_GET = $_POST = $_REQUEST = null;

        # When debugging, simulate as if been run using a proper Level 3 Authentication.

        if ($DEBUG_AS_USERNAME != null)
        {
            # Note, that this will override any other valid authentication the
            # developer might have issued.

            $debug_user_id = Db::select_value("select user_id from user where username='".
                Db::escape_string($options['DEBUG_AS_USERNAME'])."'");
            if ($debug_user_id == null)
                throw new \Exception("Invalid user name in DEBUG_AS_USERNAME: '".$options['DEBUG_AS_USERNAME']."'");
            $this->consumer = new OkapiDebugConsumer();
            $this->token = new OkapiDebugAccessToken($debug_user_id);
        }

        # Read the ETag.

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
            $this->etag = $_SERVER['HTTP_IF_NONE_MATCH'];
    }

    private function init_request()
    {
        $this->request = OAuthRequest::from_request();

        /* Verify if the request was issued with proper HTTP method. */

        if (!in_array(
            $this->request->get_normalized_http_method(),
            array('GET', 'POST')
        )) {
            throw new BadRequest("Use GET and POST methods only.");
        }

        /* Verify if the request was issued with proper okapi_base_url. */

        $url = $this->request->get_normalized_http_url();
        $allowed = false;
        foreach (Okapi::get_allowed_base_urls() as $allowed_prefix) {
            if (strpos($url, $allowed_prefix) === 0) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            throw new BadRequest(
                "Unrecognized base URL prefix! See `okapi_base_urls` field ".
                "in the `services/apisrv/installation` method. (Recommended ".
                "base URL to use is '".Okapi::get_recommended_base_url()."'.)"
            );
        }
    }

    /**
     * Return request parameter, or NULL when not found. Use this instead of
     * $_GET or $_POST or $_REQUEST.
     */
    public function get_parameter($name)
    {
        $value = $this->request->get_parameter($name);

        # Default implementation of OAuthRequest allows arrays to be passed with
        # multiple references to the same variable ("a=1&a=2&a=3"). This is invalid
        # in OKAPI and should be reported back. See issue 85:
        # https://github.com/opencaching/okapi/issues/85

        if (is_array($value))
            throw new InvalidParam($name, "Make sure you are using '$name' no more than ONCE in your URL.");
        return $value;
    }

    public function get_all_parameters_including_unknown()
    {
        return $this->request->get_parameters();
    }

    public function is_http_request() { return true; }
}
