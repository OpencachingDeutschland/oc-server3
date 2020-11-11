<?php
/****************************************************************************
 * For license information see LICENSE.md
 ****************************************************************************/

namespace OcTest\Modules\Lib2\Logic;

use OcLegacy\Util\PasswordCrypt;
use OcTest\Modules\AbstractModuleTest;

class PasswordEncryptionTest extends AbstractModuleTest
{
    /**
     * @group unit-tests
     * @covers \OcLegacy\Util\PasswordCrypt::encryptPassword
     */
    public function testPasswordEncryption(): void
    {
        global $opt;
        $opt['logic']['password_hash'] = false;

        $plain_text = 'very important data';

        $md5HashedPassword = PasswordCrypt::encryptPassword($plain_text);
        self::assertEquals('c75ac45eabed45d667359462b6a8e93e', $md5HashedPassword);

        $opt['logic']['password_hash'] = true;
        $opt['logic']['password_salt'] = '?S<,XyB1Y[y_Gz>b';

        $encryptedPassword = PasswordCrypt::encryptPassword($plain_text);
        self::assertEquals(
            '8b1d376a76e6430738d8322a6e3f4ebd5e8632f67052de7b74c8ca745bda6f11c7ea05db7de0c14bb097d3033557eb81d7fae21de988efc5353ed2f77dab504b',
            $encryptedPassword
        );
    }
}
