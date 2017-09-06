<?php

namespace okapi\CronJob;

use okapi\Db;

class TokenRevokerJob extends Cron5Job
{
    public function get_period() { return 7200; }
    public function execute()
    {
        # Remove tokens of banned users (there's no need to remove authorizations).
        # See https://github.com/opencaching/okapi/issues/432

        Db::execute("
            delete t from
                okapi_tokens t,
                user u
            where
                t.user_id = u.user_id
                and u.is_active_flag != 1
        ");
    }
}
