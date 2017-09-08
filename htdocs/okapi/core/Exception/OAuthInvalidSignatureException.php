<?php

namespace okapi\core\Exception;

/** Client's signature was invalid. */
class OAuthInvalidSignatureException extends OAuthServer401Exception {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'invalid_signature';
    }
}
