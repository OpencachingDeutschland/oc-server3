<?php

namespace OcTest\Modules\Api;

use OcTest\Modules\AbstractModuleTest;

require_once __DIR__ . '/../../../htdocs/api/rot13.php';

class Rot13Test extends AbstractModuleTest
{
    // this function didn't support nested braces
    public function test_hint_rot13_excludes_braces(): void
    {
        $string = 'a[123123]b';

        self::assertEquals('n[123123]o', \hint_rot13($string));

        $string = 'a[abcdef]b';

        self::assertEquals('n[abcdef]o', \hint_rot13($string));

        $string = 'abcdefghijklmnopqrstuvwxyz';
        self::assertEquals('nopqrstuvwxyzabcdefghijklm', \hint_rot13($string));

        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        self::assertEquals('NOPQRSTUVWXYZABCDEFGHIJKLM', \hint_rot13($string));
    }

    public function test_hint_rot13_encrypts(): void
    {
        $string = 'abcd123efgh';

        self::assertEquals('nopq123rstu', \hint_rot13($string));
    }
}
