<?php

namespace okapi\services\caches\edit;

use okapi\core\Okapi;
use okapi\core\Db;
use okapi\core\Exception\BadRequest;
use okapi\core\Exception\InvalidParam;
use okapi\core\Exception\ParamMissing;
use okapi\core\Request\OkapiRequest;
use okapi\core\Request\OkapiInternalRequest;
use okapi\core\OkapiServiceRunner;
use okapi\Settings;


class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3,
        );
    }

    public static function call(OkapiRequest $request)
    {
        $cache_code = $request->get_parameter('cache_code');
        if ($cache_code == null)
            throw new ParamMissing('cache_code');
        $geocache = OkapiServiceRunner::call(
            'services/caches/geocache',
            new OkapiInternalRequest(
                $request->consumer,
                $request->token,
                array('cache_code' => $cache_code, 'fields' => 'internal_id|type|date_created')
            )
        );
        $internal_id_escaped = Db::escape_string($geocache['internal_id']);
        $owner_id = Db::select_value(
            "select user_id from caches where cache_id = '".$internal_id_escaped."'"
        );
        if ($owner_id != $request->token->user_id)
            throw new BadRequest("Only own caches may be edited.");

        $problems = [];
        $change_sqls_escaped = [];

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $langprefs = explode("|", $langpref);

        Okapi::gettext_domain_init($langprefs);
        try
        {
            # passwd
            $newpw = $request->get_parameter('passwd');
            if ($newpw !== null)
            {
                $installation = OkapiServiceRunner::call(
                    'services/apisrv/installation',
                    new OkapiInternalRequest($request->consumer, $request->token, [])
                );
                if (strlen($newpw) > $installation['geocache_passwd_max_length']) {
                    $problems['passwd'] = sprintf(
                        _('The password must not be longer than %d characters.'),
                        $installation['geocache_passwd_max_length']
                    );
                } elseif (
                    Settings::get('OC_BRANCH') == 'oc.pl' &&
                    $geocache['type'] == 'Traditional' &&
                    $geocache['date_created'] > '2010-06-18 20:03:18'
                ) {
                    # We won't bother the user with the creation date thing here.
                    # The *current* rule is that OCPL sites do not allow tradi passwords.
                    # For older caches, the user won't see this message.

                    $problems['passwd'] = sprintf(
                        _('%s does not allow log passwords for traditional caches.'),
                        Okapi::get_normalized_site_name()
                    );
                } else {
                    $oldpw = Db::select_value("select logpw from caches where cache_id='".$internal_id_escaped."'");
                    if ($newpw != $oldpw)
                        $change_sqls_escaped[] = "logpw = '".Db::escape_string($newpw)."'";
                    unset($oldpw);
                }
            }
            unset($newpw);

            Okapi::gettext_domain_restore();
        }
        catch (Exception $e)
        {
            Okapi::gettext_domain_restore();
            throw $e;
        }

        # save changes
        if (count($problems) == 0 && count($change_sqls_escaped) > 0) {
            Db::execute("
                update caches
                set " . implode(', ', $change_sqls_escaped) . ", last_modified=NOW()
                where cache_id = '".$internal_id_escaped."'
            ");
        }

        $result = ['success' => count($problems) == 0, 'messages' => $problems];

        Okapi::update_user_activity($request);
        return Okapi::formatted_response($request, $result);
    }
}
