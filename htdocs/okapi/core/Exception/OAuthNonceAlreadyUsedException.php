<?php

namespace okapi\core\Exception;

/** Client used the same nonce for the second time. */
class OAuthNonceAlreadyUsedException extends OAuthServer400Exception {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'nonce_already_used';
    }
}
