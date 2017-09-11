<?php

namespace OcTest\Modules\Okapi;

use OcTest\Modules\AbstractModuleTest;
use okapi\core\Okapi;

require_once __DIR__ . '/../../../htdocs/okapi/autoload.php';

class PackageTest extends AbstractModuleTest
{
    public function testIfOkapiPackageIsUsed()
    {
        $message = 'please use the okapi release Package from http://rygielski.net/r/okapi-latest';

        self::assertNotNull(Okapi::$version_number, $message);
        self::assertNotNull(Okapi::$git_revision, $message);
    }
}
