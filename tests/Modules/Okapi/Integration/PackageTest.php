<?php

namespace OcTest\Modules\Okapi;

use OcTest\Modules\AbstractModuleTest;
use okapi\core\Okapi;

class PackageTest extends AbstractModuleTest
{
    public function testIfOkapiPackageIsUsed()
    {
        $message = 'please use for okapi update ./psh.phar update-okapi-package';

        self::assertNotNull(Okapi::getVersionNumber(), $message);
        self::assertNotNull(Okapi::getGitRevision(), $message);
    }
}
