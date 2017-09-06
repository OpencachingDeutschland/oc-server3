<?php

namespace okapi\Exception;

/** Client asked for an unsupported OAuth version (not 1.0). */
class OAuthVersionNotSupportedException extends OAuthServer400Exception {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['reason_stack'][] = 'unsupported_oauth_version';
    }
}
