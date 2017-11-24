<?php

namespace OcTest\Modules\OcLegacy\Util;

use OcLegacy\Util\PasswordCrypt;
use OcTest\Modules\AbstractModuleTest;

class PasswordCryptTest extends AbstractModuleTest
{
    public function test_encryptPassword()
    {
        global $opt;
        $opt['logic']['password_hash'] = true;
        $opt['logic']['password_salt'] = 'salting_the_world';

        self::assertEquals(
            '757d277993df26d3ad40ca9d413bf53305b2957eae335ed9c8782e33708d341d626668030490d0373651b4f744d716cccbe886eee4654d3ce29fb574fc04b320',
            PasswordCrypt::encryptPassword('password')
        );
    }

    public function test_encryptPassword_without_salt()
    {
        global $opt;
        $opt['logic']['password_hash'] = false;

        self::assertEquals('5f4dcc3b5aa765d61d8327deb882cf99', PasswordCrypt::encryptPassword('password'));
    }
}
