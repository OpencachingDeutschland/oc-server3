<?php

namespace OcTest\Modules\Okapi;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../htdocs/okapi/core.php';

class PackageTest extends AbstractModuleTest
{
    public function testIfOkapiPackageIsUsed()
    {
        $message = 'please use the okapi release Package from http://rygielski.net/r/okapi-latest';

        self::assertNotNull(\okapi\Okapi::$version_number, $message);
        self::assertNotNull(\okapi\Okapi::$git_revision, $message);
    }
}
