<?php

namespace okapi\Exception;

/** OAuth server errors which should result in HTTP 401 response. */
abstract class OAuthServer401Exception extends OAuthServerException {
    protected function provideExtras(&$extras) {
        parent::provideExtras($extras);
        $extras['status'] = 401;
    }
    public function getHttpStatusCode() { return 401; }
}
