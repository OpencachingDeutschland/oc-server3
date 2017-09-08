<?php

namespace okapi\core\Exception;

/** Client provider invalid token (either Request Token or Access Token). */
class OAuthInvalidTokenException extends OAuthServer401Exception {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'invalid_token';
    }
}
