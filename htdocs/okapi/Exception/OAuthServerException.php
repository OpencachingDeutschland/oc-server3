<?php

namespace okapi\Exception;

/** OAuth server errors. */
abstract class OAuthServerException extends OAuthException {
    abstract public function getHttpStatusCode();
    protected function provideExtras(&$extras) {
        $extras['reason_stack'][] = 'invalid_oauth_request';
    }
    public function getOkapiJSON() {
        $extras = array(
            'developer_message' => $this->getMessage(),
            'reason_stack' => array(),
        );
        $this->provideExtras($extras);
        $extras['more_info'] = "https://opencaching.pl/okapi/introduction.html#errors";
        return json_encode(array("error" => $extras));
    }
}
