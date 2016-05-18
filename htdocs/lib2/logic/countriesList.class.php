<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class countriesList
{
    public $locale;
    public $bDefaultUsed = false;

    public function __construct($locale = null)
    {
        global $opt;

        $this->locale = $locale;
        if ($locale === null) {
            $this->locale = $opt['template']['locale'];
        }
    }

    public function defaultUsed()
    {
        return $this->bDefaultUsed;
    }

    public function isDefault($id)
    {
        $isDefault = sql_value(
            "SELECT COUNT(*) FROM `countries_list_default` WHERE `lang`='&1' AND `show`='&2'",
            0,
            $this->locale,
            $id
        );

        if ($isDefault == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getDefaultRS()
    {
        $isDefault = sql_value("SELECT COUNT(*) FROM `countries_list_default` WHERE `lang`='&1'", 0, $this->locale);
        if ($isDefault == 0) {
            return $this->getAllRS();
        }

        $this->bDefaultUsed = true;

        $returnValue = sql(
            "SELECT
                `countries`.`short` AS `id`,
                IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name`
             FROM `countries`
             INNER JOIN `countries_list_default`
                 ON `countries`.`short`=`countries_list_default`.`show`
                 AND `countries_list_default`.`lang`='&1'
             LEFT JOIN `sys_trans`
                 ON `countries`.`trans_id`=`sys_trans`.`id`
                 AND `countries`.`name`=`sys_trans`.`text`
             LEFT JOIN `sys_trans_text`
                 ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                 AND `sys_trans_text`.`lang`='&1'
             ORDER BY `name`",
            $this->locale
        );

        return $returnValue;
    }

    public function getAllRS()
    {
        $this->bDefaultUsed = false;

        $returnValue = sql(
            "SELECT
                 `countries`.`short` AS `id`,
                 IFNULL(`sys_trans_text`.`text`,
                 `countries`.`name`) AS `name`
             FROM `countries`
             LEFT JOIN `sys_trans`
                 ON `countries`.`trans_id`=`sys_trans`.`id`
                 AND `countries`.`name`=`sys_trans`.`text`
             LEFT JOIN `sys_trans_text`
                 ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                 AND `sys_trans_text`.`lang`='&1'
             ORDER BY `name`",
            $this->locale
        );

        return $returnValue;
    }

    public function getRS($selectedId, $showall)
    {
        if ($showall != false) {
            return $this->getAllRS();
        }

        if ($selectedId !== null && !$this->isDefault($selectedId)) {
            return $this->getAllRS();
        }

        return $this->getDefaultRS();
    }

    public static function getCountryLocaleName($id)
    {
        global $opt;

        $returnValue = sql_value(
            "SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`)
             FROM `countries`
             LEFT JOIN `sys_trans`
                 ON `countries`.`trans_id`=`sys_trans`.`id`
             LEFT JOIN `sys_trans_text`
                 ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                 AND `sys_trans_text`.`lang`='&2'
             WHERE `countries`.`short`='&1'",
            '',
            $id,
            $opt['template']['locale']
        );

        return $returnValue;
    }
}
