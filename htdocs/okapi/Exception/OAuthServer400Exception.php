<?php

namespace okapi\Exception;

/** OAuth server errors which should result in HTTP 400 response. */
abstract class OAuthServer400Exception extends OAuthServerException {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['status'] = 400;
    }
    public function getHttpStatusCode() { return 400; }
}
