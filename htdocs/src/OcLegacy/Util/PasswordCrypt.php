<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Cryptographic library for encryption of passwords.
 ***************************************************************************/

namespace OcLegacy\Util;

class PasswordCrypt
{
    public static function encryptPassword($password)
    {
        // Calls the password encryption chained
        $md5 = self::firstStagePasswordEncryption($password);

        return self::secondStagePasswordEncryption($md5);
    }

    public static function firstStagePasswordEncryption($password)
    {
        return md5($password);
    }

    public static function secondStagePasswordEncryption($password)
    {
        global $opt;
        if ($opt['logic']['password_hash']) {
            return hash_hmac('sha512', $password, $opt['logic']['password_salt']);
        }

        return $password;
    }
}
