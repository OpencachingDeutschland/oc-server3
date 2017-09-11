<?php

namespace okapi\core\Exception;

/** Client used unsupported signature method. */
class OAuthUnsupportedSignatureMethodException extends OAuthServer400Exception {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'unsupported_signature_method';
    }
}
