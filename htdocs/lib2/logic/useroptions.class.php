<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *   set has to be commited with save
 *
 ***************************************************************************/

require_once __DIR__ . '/const.inc.php';

class useroptions
{
    /** @var int $nUserId */
    public $nUserId = 0;

    /** @var array $nOptions */
    public $nOptions;

    public function __construct($nUserId = ID_NEW)
    {
        $this->nUserId = $nUserId + 0;

        if ($nUserId == ID_NEW) {
            $rs = sqll(
                'SELECT
                     `id`,
                     `name`,
                     `default_value`,
                     `check_regex`,
                     `option_order`, 0 AS `option_visible`,
                     `internal_use`, `default_value` AS `option_value`,
                     `optionset`
                 FROM `profile_options`'
            );
        } else {
            $rs = sqll(
                "SELECT
                     `p`.`id`,
                     `p`.`name`,
                     `p`.`default_value`,
                     `p`.`check_regex`,
                     `p`.`option_order`,
                     IFNULL(`u`.`option_visible`, 0) AS `option_visible`,
                     `p`.`internal_use`,
                     IFNULL(`u`.`option_value`, `p`.`default_value`) AS `option_value`
                 FROM `profile_options` AS `p`
                 LEFT JOIN `user_options` AS `u`
                     ON `p`.`id`=`u`.`option_id`
                     AND (`u`.`user_id` IS NULL OR `u`.`user_id`='&1')
                 UNION
                 SELECT
                     `u`.`option_id` AS `id`,
                     `p`.`name`,
                     `p`.`default_value`,
                     `p`.`check_regex`,
                     `p`.`option_order`,
                     `u`.`option_visible`,
                     `p`.`internal_use`,
                     IFNULL(`u`.`option_value`, `p`.`default_value`) AS `option_value`
                 FROM `user_options` AS `u`
                 LEFT JOIN `profile_options` AS `p` ON `p`.`id`=`u`.`option_id`
                 WHERE `u`.`user_id`='&1'",
                $this->nUserId
            );
        }

        while ($record = sql_fetch_array($rs)) {
            $this->nOptions[$record['id']] = $record;
        }

        sql_free_result($rs);
    }

    public function getUserId()
    {
        return $this->nUserId;
    }

    public function getOptSet($pId)
    {
        return $this->nOptions[$pId]['optionset'];
    }

    public function getOptName($pId)
    {
        return $this->nOptions[$pId]['name'];
    }

    public function getOptDefault($pId)
    {
        return $this->nOptions[$pId]['default_value'];
    }

    public function getOptRegex($pId)
    {
        return $this->nOptions[$pId]['option_regex'];
    }

    public function getOptOrder($pId)
    {
        return $this->nOptions[$pId]['option_order'];
    }

    public function getOptVisible($pId)
    {
        return $this->nOptions[$pId]['option_visible'];
    }

    public function getOptInternal($pId)
    {
        return $this->nOptions[$pId]['internal_use'];
    }

    public function getOptValue($pId)
    {
        if ($pId == USR_OPT_SHOWSTATS &&
            sql_value("SELECT `is_active_flag` FROM `user` WHERE `user_id`='&1'", 0, $this->nUserId) == 0
        ) {
            // User profile options are deleted when an account is disabled. This will
            // enable USR_OPT_SHOWSTATS which is 1 by default. We encounter this by
            // forcing USR_OPT_SHOWSTATS = 0 for disabled users.
            return 0;
        } elseif (array_key_exists($pId, $this->nOptions)) {
            return $this->nOptions[$pId]['option_value'];
        }

        return false;
    }

    public function setOptVisible($pId, $pValue)
    {
        $pId += 0;
        $pValue += 0;

        if ($pValue != 1 || $this->nOptions[$pId]['internal_use'] == 1) {
            $pValue = 0;
        }

        $this->nOptions[$pId]['option_visible'] = $pValue;

        return true;
    }

    public function setOptValue($pId, $pValue)
    {
        $pId += 0;
        if ($this->nOptions[$pId]['check_regex'] == '') {
            $this->nOptions[$pId]['option_value'] = $pValue;

            return true;
        } elseif (preg_match("/" . $this->nOptions[$pId]['check_regex'] . "/", $pValue) || strlen($pValue) == 0) {
            $this->nOptions[$pId]['option_value'] = $pValue;

            return true;
        }

        return false;
    }

    // return if successfull (with insert)
    public function save()
    {
        foreach ($this->nOptions as $record) {
            sqll(
                "INSERT INTO `user_options` (`user_id`, `option_id`, `option_visible`, `option_value`)
                 VALUES ('&1', '&2', '&3', '&4') ON DUPLICATE KEY UPDATE `option_visible`='&3', `option_value`='&4'",
                $this->nUserId,
                $record['id'],
                $record['option_visible'],
                $record['option_value']
            );
        }

        return true;
    }
}
