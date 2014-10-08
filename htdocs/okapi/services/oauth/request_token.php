<?

namespace okapi\services\oauth\request_token;

use okapi\Okapi;
use okapi\OkapiHttpResponse;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 2
        );
    }

    public static function call(OkapiRequest $request)
    {
        $callback = $request->get_parameter('oauth_callback');
        if (!$callback)
        {
            # We require the 1.0a flow (throw an error when there is no oauth_callback).
            throw new ParamMissing("oauth_callback");
        }

        $new_token = Okapi::$data_store->new_request_token($request->consumer, $callback);

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        $response->body = $new_token."&oauth_callback_confirmed=true";
        return $response;
    }
}
