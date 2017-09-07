<?php

namespace okapi\Exception;

/** Client provided invalid Consumer Key. */
class OAuthInvalidConsumerException extends OAuthServer401Exception {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'invalid_consumer';
    }
}
