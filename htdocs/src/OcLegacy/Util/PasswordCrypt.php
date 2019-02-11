<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *  Cryptographic library for encryption of passwords.
 ***************************************************************************/

namespace OcLegacy\Util;

class PasswordCrypt
{
    public static function encryptPassword(string $password): string
    {
        // Calls the password encryption chained
        $md5 = self::firstStagePasswordEncryption($password);

        return self::secondStagePasswordEncryption($md5);
    }

    public static function firstStagePasswordEncryption(string $password): string
    {
        return md5($password);
    }

    public static function secondStagePasswordEncryption(string $password): string
    {
        global $opt;
        if ($opt['logic']['password_hash']) {
            return hash_hmac('sha512', $password, $opt['logic']['password_salt']);
        }

        return $password;
    }
}
