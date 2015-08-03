<?php

namespace okapi;

use OAuthDataStore;

class OkapiDataStore extends OAuthDataStore
{
    public function lookup_consumer($consumer_key)
    {
        $row = Db::select_row("
            select `key`, secret, name, url, email, bflags
            from okapi_consumers
            where `key` = '".mysql_real_escape_string($consumer_key)."'
        ");
        if (!$row)
            return null;
        return new OkapiConsumer($row['key'], $row['secret'], $row['name'],
            $row['url'], $row['email'], $row['bflags']);
    }

    public function lookup_token($consumer, $token_type, $token)
    {
        $row = Db::select_row("
            select `key`, consumer_key, secret, token_type, user_id, verifier, callback
            from okapi_tokens
            where
                consumer_key = '".mysql_real_escape_string($consumer->key)."'
                and token_type = '".mysql_real_escape_string($token_type)."'
                and `key` = '".mysql_real_escape_string($token)."'
        ");
        if (!$row)
            return null;
        switch ($row['token_type'])
        {
            case 'request':
                return new OkapiRequestToken($row['key'], $row['secret'],
                    $row['consumer_key'], $row['callback'], $row['user_id'],
                    $row['verifier']);
            case 'access':
                return new OkapiAccessToken($row['key'], $row['secret'],
                    $row['consumer_key'], $row['user_id']);
            default:
                throw new Exception();
        }
    }

    public function lookup_nonce($consumer, $token, $nonce, $timestamp)
    {
        # Since it's not important for us to save the actual token and nonce
        # value, we will save a hash only. We could also include the consumer
        # key in this hash and drop the column, but we will leave it be for
        # now (for a couple of less important reasons).

        $nonce_hash = md5(serialize(array(
            $token ? $token->key : null,
            $timestamp,
            $nonce
        )));
        try
        {
            # Time timestamp is saved separately, because we are periodically
            # removing older nonces from the database (see cronjobs).

            Db::execute("
                insert into okapi_nonces (consumer_key, nonce_hash, timestamp)
                values (
                    '".mysql_real_escape_string($consumer->key)."',
                    '".mysql_real_escape_string($nonce_hash)."',
                    '".mysql_real_escape_string($timestamp)."'
                );
            ");
            return null;
        }
        catch (\Exception $e)
        {
            # INSERT failed. This nonce was already used.

            return $nonce;
        }
    }

    public function new_request_token($consumer, $callback = null)
    {
        if ((preg_match("#^[a-z][a-z0-9_.-]*://#", $callback) > 0) ||
            $callback == "oob")
        { /* ok */ }
        else { throw new BadRequest("oauth_callback should begin with lower case <scheme>://, or should equal 'oob'."); }
        $token = new OkapiRequestToken(Okapi::generate_key(20), Okapi::generate_key(40),
            $consumer->key, $callback, null, Okapi::generate_key(8, true));
        Db::execute("
            insert into okapi_tokens
                (`key`, secret, token_type, timestamp,
                user_id, consumer_key, verifier, callback)
            values (
                '".mysql_real_escape_string($token->key)."',
                '".mysql_real_escape_string($token->secret)."',
                'request',
                unix_timestamp(),
                null,
                '".mysql_real_escape_string($consumer->key)."',
                '".mysql_real_escape_string($token->verifier)."',
                ".(($token->callback_url == 'oob')
                    ? "null"
                    : "'".mysql_real_escape_string($token->callback_url)."'"
                )."
            );
        ");
        return $token;
    }

    public function new_access_token($token, $consumer, $verifier = null)
    {
        if ($token->consumer_key != $consumer->key)
            throw new BadRequest("Request Token given is not associated with the Consumer who signed the request.");
        if (!$token->authorized_by_user_id)
            throw new BadRequest("Request Token given has not been authorized.");
        if ($token->verifier != $verifier)
            throw new BadRequest("Invalid verifier.");

        # Invalidate the Request Token.

        Db::execute("
            delete from okapi_tokens
            where `key` = '".mysql_real_escape_string($token->key)."'
        ");

        # In OKAPI, all Access Tokens are long lived. Therefore, we don't want
        # to generate a new one every time a Consumer wants it. We will check
        # if there is already an Access Token generated for this (Consumer, User)
        # pair and return it if there is.

        $row = Db::select_row("
            select `key`, secret
            from okapi_tokens
            where
                token_type = 'access'
                and user_id = '".mysql_real_escape_string($token->authorized_by_user_id)."'
                and consumer_key = '".mysql_real_escape_string($consumer->key)."'
        ");
        if ($row)
        {
            # Use existing Access Token

            $access_token = new OkapiAccessToken($row['key'], $row['secret'],
                $consumer->key, $token->authorized_by_user_id);
        }
        else
        {
            # Generate a new Access Token.

            $access_token = new OkapiAccessToken(Okapi::generate_key(20), Okapi::generate_key(40),
                $consumer->key, $token->authorized_by_user_id);
            Db::execute("
                insert into okapi_tokens
                    (`key`, secret, token_type, timestamp, user_id, consumer_key)
                values (
                    '".mysql_real_escape_string($access_token->key)."',
                    '".mysql_real_escape_string($access_token->secret)."',
                    'access',
                    unix_timestamp(),
                    '".mysql_real_escape_string($access_token->user_id)."',
                    '".mysql_real_escape_string($consumer->key)."'
                );
            ");
        }
        return $access_token;
    }

    public function cleanup()
    {
        Db::execute("
            delete from okapi_nonces
            where
                timestamp < unix_timestamp(date_add(now(), interval -6 minute))
                or timestamp > unix_timestamp(date_add(now(), interval 6 minute))
        ");
        Db::execute("
            delete from okapi_tokens
            where
                token_type = 'request'
                and timestamp < unix_timestamp(date_add(now(), interval -2 hour))
        ");
    }
}
