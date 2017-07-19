<?php

/***************************************************************************
 * for license information see LICENSE.md
 *  summarize methods to get new events, caches, ratings, etc.
 ***************************************************************************/
class getNew
{
    private $userCountry;

    /**
     * @return mixed
     */
    public function get_userCountry()
    {
        return $this->userCountry;
    }

    /**
     * @param $userCountry
     */
    public function set_userCountry($userCountry)
    {
        $this->userCountry = $userCountry;
    }

    /**
     * constructor
     * creates the object
     *
     * @param string $userCountry country of the loggedin user as parameter for the sql statements
     */
    public function __construct($userCountry)
    {
        $this->set_userCountry($userCountry);
    }

    /**
     * rsForSmarty creates the result from database to use with smarty assign-rs method
     * based on $this->type
     *
     * @param string $type type of the "new"-information, i.e. cache, event, rating, etc
     * @param array $args numeric array containing the parameter for "sql_slave"
     * @return object mysql result used by smarty assign_rs
     */
    public function rsForSmarty($type, $args = null)
    {
        switch ($type) {
            case 'cache':
                return $this->cacheRs($args);
                break;
            case 'event':
                return $this->eventRs($args);
                break;
            case 'rating':
                return $this->ratingRs($args);
                break;
        }
    }

    /**
     * feedForSmarty creates a HTML string to use with smarty assign method
     * based on $this->type by using RSSParser class
     *
     * @param string $type type of the "new"-information, i.e. cache, event, rating, etc
     * @param int $items number of feeditems to parse from feed (RSSParser)
     * @param string $url url of the feed to parse (RSSParser)
     * @param int $timeout maximum seconds to wait for the requested page
     * @param null $includeText
     * @return string HTML string used for smarty assign method
     * @internal param bool $includetext ???following??? add table-tag?
     */
    public function feedForSmarty($type, $items = null, $url = null, $timeout = null, $includeText = null)
    {
        switch ($type) {
            case 'forum':
                return $this->forumFeed($items, $url, $timeout, $includeText);
                break;
            case 'blog':
                return $this->blogFeed($items, $url, $timeout, $includeText);
                break;
            case 'wiki':
                return $this->wikiFeed($items, $url, $timeout, $includeText);
                break;
        }
    }

    /**
     * cacheRs executes the database statements for type "cache"
     *
     * @param array $args numeric array containing the parameter for "sql_slave"
     * @return object mysql result used by smarty assign_rs
     */
    private function cacheRs($args = null)
    {
        global $opt;

        if ($args === null || !is_array($args)) {
            $args = [
                $this->get_userCountry(),
                $opt['template']['locale'],
                10,
            ];
        }

        return sql_slave(
            "SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`date_created` `date_created`,
                `caches`.`type`,
                `caches`.`longitude` `longitude`,
                `caches`.`latitude` `latitude`,
                IFNULL(`sys_trans_text`.`text`,`countries`.`en`) AS `adm1`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm2`,'') `adm2`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm3`,'') `adm3`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm4`,'') `adm4`,
                `ca`.`attrib_id` IS NOT NULL AS `oconly`
            FROM `caches`
            INNER JOIN `user`
              ON `user`.`user_id`=`caches`.`user_id`
            LEFT JOIN `cache_location`
              ON `caches`.`cache_id`=`cache_location`.`cache_id`
            LEFT JOIN `countries`
              ON `countries`.`short`=`caches`.`country`
            LEFT JOIN `sys_trans_text`
              ON `sys_trans_text`.`trans_id`=`countries`.`trans_id`
              AND `sys_trans_text`.`lang`='&2'
            LEFT JOIN `caches_attributes` `ca`
              ON `ca`.`cache_id`=`caches`.`cache_id`
              AND `ca`.`attrib_id`=6
            WHERE `caches`.`country`='&1'
              AND `caches`.`type` != 6
              AND `caches`.`status` = 1
            ORDER BY `caches`.`date_created` DESC
            LIMIT 0, &3",
            $args
        );
    }

    /**
     * eventRs executes the database statements for type "event"
     *
     * @param array $args numeric array containing the parameter for "sql_slave"
     * @return object mysql result used by smarty assign_rs
     */
    private function eventRs($args = null)
    {
        global $opt;

        if ($args === null || !is_array($args)) {
            $args = [
                $this->get_userCountry(),
                $opt['template']['locale'],
                10,
            ];
        }

        return sql_slave(
            "SELECT `user`.`user_id` `user_id`,
                `user`.`username` `username`,
                `caches`.`cache_id` `cache_id`,
                `caches`.`name` `name`,
                `caches`.`date_hidden`,
                IFNULL(`sys_trans_text`.`text`,`countries`.`en`) AS `adm1`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm2`,'') `adm2`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm3`,'') `adm3`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm4`,'') `adm4`,
                `ca`.`attrib_id` IS NOT NULL AS `oconly`
            FROM `caches`
            INNER JOIN `user`
              ON `user`.`user_id`=`caches`.`user_id`
            LEFT JOIN `cache_location`
              ON `caches`.`cache_id`=`cache_location`.`cache_id`
            LEFT JOIN `countries`
              ON `countries`.`short`=`caches`.`country`
            LEFT JOIN `sys_trans_text`
              ON `sys_trans_text`.`trans_id`=`countries`.`trans_id`
              AND `sys_trans_text`.`lang`='&2'
            LEFT JOIN `caches_attributes` `ca`
              ON `ca`.`cache_id`=`caches`.`cache_id`
              AND `ca`.`attrib_id`=6
            WHERE `caches`.`country`='&1' 
              AND `caches`.`date_hidden` >= curdate()
              AND `caches`.`type` = 6
              AND `caches`.`status`=1
            ORDER BY `date_hidden` ASC
            LIMIT 0, &3",
            $args
        );
    }

    /**
     * ratingDays returns the number of days used for top rating calculation
     *
     * @return integer
     */
    public function ratingDays()
    {
        global $opt;

        // Calculate days depending on country selection.
        // Todo: make default country configurable and use this also for
        // "except of [Germany]" new caches and logs lists

        if ($this->get_userCountry() === 'DE') {
            return $opt['logic']['rating']['topdays_mainCountry'];
        } else {
            return $opt['logic']['rating']['topdays_otherCountry'];
        }
    }

    /**
     * ratingRs executes the database statements for type "rating"
     *
     * @param array $args numeric array containing the parameter for "sql_slave"
     * @return object mysql result used by smarty assign_rs
     */
    private function ratingRs($args = null)
    {
        global $opt;

        // check $args and set defaults
        if ($args === null || !is_array($args)) {
            $args = [
                $this->get_userCountry(),
                $opt['template']['locale'],
                10,
                $this->ratingDays(),
            ];
        }

        // execute sql
        // 2012-08-24 following
        //   optimized by adding rating_date field to cache_rating, so we don't need the log table.
        return sql_slave(
            "SELECT COUNT(`cache_rating`.`user_id`) AS `cRatings`,
                MAX(`cache_rating`.`rating_date`) AS `dLastLog`,
                `user`.`user_id` AS `user_id`,
                `user`.`username` AS `username`,
                `caches`.`cache_id` AS `cache_id`,
                `caches`.`name` AS `name`,
                `caches`.`type`,
                IFNULL(`sys_trans_text`.`text`,`countries`.`en`) AS `adm1`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm2`,'') `adm2`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm3`,'') `adm3`,
                IF(`caches`.`country`=`cache_location`.`code1`,`cache_location`.`adm4`,'') `adm4`,
                `ca`.`attrib_id` IS NOT NULL AS `oconly`
            FROM `cache_rating`
                INNER JOIN `caches` ON `caches`.`cache_id`=`cache_rating`.`cache_id`
                INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
                LEFT JOIN `cache_location` ON `cache_rating`.`cache_id`=`cache_location`.`cache_id`
                LEFT JOIN `countries` ON `countries`.`short`=`caches`.`country`
                LEFT JOIN `sys_trans_text`
                  ON `sys_trans_text`.`trans_id`=`countries`.`trans_id`
                  AND `sys_trans_text`.`lang`='&2'
                LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
            WHERE `caches`.`country`='&1' AND
                `cache_rating`.`rating_date`>DATE_SUB(NOW(), INTERVAL &4 DAY) AND
                `caches`.`type`!=6 AND
                `caches`.`status`=1
            GROUP BY `cache_rating`.`cache_id`
            ORDER BY `cRatings` DESC,
                `dLastLog` DESC,
                `cache_id` DESC
            LIMIT 0, &3",
            $args
        );
    }


    /**
     * blogFeed executes the RSSParser for type "blog"
     *
     * @param int $items number of feeditems to parse from feed (RSSParser)
     * @param string $url url of the feed to parse (RSSParser)
     * @param int $timeout maximum seconds to wait for the requested page
     * @param boolean $includeText ???following??? add table-tag?
     * @return string HTML string used for smarty assign method
     */
    private function blogFeed($items = null, $url = null, $timeout = null, $includeText = null)
    {
        global $opt;

        if (!is_numeric($items) || $items === null) {
            $items = $opt['news']['count'];
        }

        if ($url === null || !is_string($url)) {
            $url = $opt['news']['include'];
        }

        if ($timeout === null || !is_numeric($timeout)) {
            $timeout = $opt['news']['timeout'];
        }

        if ($includeText === null || !is_bool($includeText)) {
            $includeText = false;
        }

        return RSSParser::parse($items, $url, $timeout, $includeText);
    }

    /**
     * forumFeed executes the RSSParser for type "forum"
     *
     * @param int $items number of feeditems to parse from feed (RSSParser)
     * @param string $url url of the feed to parse (RSSParser)
     * @param int $timeout maximum seconds to wait for the requested page
     * @param boolean $includeText ???following??? add table-tag?
     * @return string HTML string used for smarty assign method
     */
    private function forumFeed($items = null, $url = null, $timeout = null, $includeText = null)
    {
        global $opt;

        if (!is_numeric($items) || $items === null) {
            $items = $opt['forum']['count'];
        }

        if ($url === null || !is_string($url)) {
            $url = $opt['forum']['url'];
        }
        if ($timeout === null || !is_numeric($timeout)) {
            $timeout = $opt['forum']['timeout'];
        }

        if ($includeText === null || !is_bool($includeText)) {
            $includeText = false;
        }

        return RSSParser::parse($items, $url, $timeout, $includeText);
    }

    /**
     * wikiFeed executes the RSSParser for type "wiki"
     *
     * @param int $items number of feeditems to parse from feed (RSSParser)
     * @param string $url url of the feed to parse (RSSParser)
     * @param boolean $includeText ???following??? add table-tag?
     * @param int $timeout maximum seconds to wait for the requested page
     * @return string HTML string used for smarty assign method
     */
    private function wikiFeed($items = null, $url = null, $timeout = null, $includeText = null)
    {
        global $opt;

        if (!is_numeric($items) || $items === null) {
            $items = $opt['wikinews']['count'];
        }

        if (!is_string($url) || $url === null) {
            $url = $opt['wikinews']['url'];
        }

        if ($timeout === null || !is_numeric($timeout)) {
            $timeout = $opt['wikinews']['timeout'];
        }

        if ($includeText === null || !is_bool($includeText)) {
            $includeText = false;
        }

        // execute RSSParser
        return RSSParser::parse($items, $url, $timeout, $includeText);
    }
}
