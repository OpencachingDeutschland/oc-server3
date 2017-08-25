<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Country;

class Country
{
    private $country;
    private $threshold;
    private $totalCaches;
    private $countryCaches;

    public function __construct($country, $purpose)
    {
        global $opt;

        $this->country = $country;
        $this->threshold = $opt['logic']['main_countries'][$purpose];
        $this->totalCaches = 0;
        $this->countryCaches = 0;
    }

    // simple query function that works with both lib1 and lib2
    private static function sqlvalue($query, $default, $a=false, $b=false, $c=false)
    {
        $rs = sql($query, $a, $b, $c);
        $r = sql_fetch_row($rs);
        if ($r && count($r)) {
            return $r[0];
        }
        else {
          return $default;
        }
    }

    public function getLocaleName()
    {
        global $opt;

        return Country::sqlvalue(
            "SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`)
             FROM `countries`
             LEFT JOIN `sys_trans`
                 ON `countries`.`trans_id`=`sys_trans`.`id`
             LEFT JOIN `sys_trans_text`
                 ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                 AND `sys_trans_text`.`lang`='&2'
             WHERE `countries`.`short`='&1'",
            '',
            $this->country,
            $opt['template']['locale']
        );
    }

    public function getTotalCaches()
    {
        if ($this->totalCaches == 0) {
            $this->totalCaches = Country::sqlvalue(
                "SELECT SUM(`active_caches`)
                 FROM `stat_cache_countries`",
                0
            );
        }
        return $this->totalCaches;
    }

    public function getCountryCaches()
    {
        if ($this->countryCaches == 0) {
            $this->countryCaches = Country::sqlvalue(
                "SELECT `active_caches`
                 FROM `stat_cache_countries`
                 WHERE `country`='&1'",
                0,
                $this->country
            );
        }
        return $this->countryCaches; 
    }

    private function getThresholdCaches()
    {
        return $this->getTotalCaches() * $this->threshold;
    }
    
    public function isMain()
    {
        return ($this->getCountryCaches() >= $this->getThresholdCaches()); 
    }

    public function getMainRS()
    {
        return Country::getConditionalRS(
            "`stat_cache_countries`.`active_caches` >= '" . sql_escape($this->getThresholdCaches()) . "'"
        );
    }

    public function getAllRS()
    {
        global $opt;

        $returnValue = sql(
            "SELECT
                 `countries`.`short` AS `code`,
                 IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name`
             FROM `countries`
             LEFT JOIN `sys_trans`
                 ON `countries`.`trans_id`=`sys_trans`.`id`
                 AND `countries`.`name`=`sys_trans`.`text`
             LEFT JOIN `sys_trans_text`
                 ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                 AND `sys_trans_text`.`lang`='&1'
             ORDER BY `name`",
            $opt['template']['locale']
        );

        return $returnValue;
    }

    public function getRS()
    {
        if ($this->isMain()) {
            return $this->getMainRS();
        } else {
            return $this->getAllRS();
        }
    }
    
    public function getGobalSelectionList($minimum)
    {
        $threshold = sql_escape($this->getThresholdCaches());
        $minimum = sql_escape($minimum);

        return array_merge(
            $this->getCountryList("`active_caches`>='" . $threshold . "'"),
            [false],
            $this->getCountryList("`active_caches`>'" . $minimum . "' AND `active_caches` < '" . $threshold . "'")
        );
    }

    private static function getConditionalRS($condition_sql)
    {
        global $opt;

        return sql(
            "SELECT
                 `countries`.`short` AS `code`,
                 IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name`
             FROM `countries`
             INNER JOIN `stat_cache_countries`
                 ON `stat_cache_countries`.`country`=`countries`.`short`
             LEFT JOIN `sys_trans`
                 ON `countries`.`trans_id`=`sys_trans`.`id`
                 AND `countries`.`name`=`sys_trans`.`text`
             LEFT JOIN `sys_trans_text`
                 ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                 AND `sys_trans_text`.`lang`='&1'
             WHERE $condition_sql
             ORDER BY `name`",
            $opt['template']['locale']
        );
    }

    private static function getCountryList($condition_sql)
    {
        $rs = Country::getConditionalRS($condition_sql);
        $returnValue = [];
        while ($r = sql_fetch_assoc($rs))
          $returnValue[] = $r;
        sql_free_result($rs);
        return $returnValue;
    }
}
