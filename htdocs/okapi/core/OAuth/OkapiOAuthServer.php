<?php

namespace okapi\core\OAuth;

use okapi\core\Exception\OAuthMissingParameterException;

/** Default OAuthServer with some OKAPI-specific methods added. */
class OkapiOAuthServer extends OAuthServer
{
    public function __construct($data_store)
    {
        parent::__construct($data_store);

        # https://github.com/opencaching/okapi/issues/475

        $this->add_signature_method(new OAuthSignatureMethod_HMAC_SHA1());
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            # Request was made over HTTPS. Allow PLAINTEXT method.
            $this->add_signature_method(new OAuthSignatureMethod_PLAINTEXT());
        }
    }

    /**
     * By default, works like verify_request, but it does support some additional
     * options. If $token_required == false, it doesn't throw an exception when
     * there is no token specified. You may also change the token_type required
     * for this request.
     */
    public function verify_request2(&$request, $token_type = 'access', $token_required = true)
    {
        $this->get_version($request);
        $consumer = $this->get_consumer($request);
        try {
            $token = $this->get_token($request, $consumer, $token_type);
        } catch (OAuthMissingParameterException $e) {
            # Note, that exception will be different if token is supplied
            # and is invalid. We catch only a completely MISSING token parameter.
            if (($e->getParamName() == 'oauth_token') && (!$token_required))
                $token = null;
            else
                throw $e;
        }
        $this->check_signature($request, $consumer, $token);
        return array($consumer, $token);
    }
}
