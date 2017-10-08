<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

class attribute
{
    /* array with all attributes grouped by attribute group */
    public static function getAttributesListArray($firstLetterUppercase = false)
    {
        return self::getAttributesListArrayInternal(0, false, $firstLetterUppercase);
    }

    public static function getSelectableAttributesListArray($firstLetterUppercase = false)
    {
        return self::getAttributesListArrayInternal(0, true, $firstLetterUppercase);
    }

    public static function getAttributesListArrayByCacheId($cacheId, $firstLetterUppercase = false)
    {
        return self::getAttributesListArrayInternal($cacheId, false, $firstLetterUppercase);
    }

    /**
     * @param $cacheId
     * @param boolean $bOnlySelectable
     * @param boolean $firstLetterUppercase
     * @return array
     */
    public static function getAttributesListArrayInternal($cacheId, $bOnlySelectable, $firstLetterUppercase)
    {
        global $opt;

        $attributes = array();
        $rsAttrGroup = sql(
            "SELECT `attribute_groups`.`id`,
                     IFNULL(`tt1`.`text`, `attribute_groups`.`name`) AS `name`,
                     IFNULL(`tt2`.`text`, `attribute_categories`.`name`) AS `category`,
                     `attribute_categories`.`color`
            FROM `attribute_groups`
            INNER JOIN `attribute_categories`
                ON `attribute_groups`.`category_id`=`attribute_categories`.`id`
            LEFT JOIN `sys_trans` AS `t1`
                ON `attribute_groups`.`trans_id`=`t1`.`id`
                AND `attribute_groups`.`name`=`t1`.`text`
            LEFT JOIN `sys_trans_text` AS `tt1`
                ON `t1`.`id`=`tt1`.`trans_id`
                AND `tt1`.`lang`='&1'
            LEFT JOIN `sys_trans` AS `t2`
                ON `attribute_categories`.`trans_id`=`t2`.`id`
                AND `attribute_categories`.`name`=`t2`.`text`
            LEFT JOIN `sys_trans_text` AS `tt2`
                ON `t2`.`id`=`tt2`.`trans_id`
                AND `tt2`.`lang`='&1'
            ORDER BY `attribute_groups`.`id` ASC",
            $opt['template']['locale']
        );
        while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup)) {
            $attr = array();
            $bSearchGroupDefault = false;

            if ($cacheId == 0) {
                $sAddWhereSql = '';
                if ($bOnlySelectable == true) {
                    $sAddWhereSql .= ' AND `cache_attrib`.`selectable`=1';
                }

                $rsAttr = sql(
                    "SELECT `cache_attrib`.`id`,
                            IFNULL(`tt1`.`text`, `cache_attrib`.`name`) AS `name`,
                            IFNULL(`tt2`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`,
                            `cache_attrib`.`icon`, `cache_attrib`.`search_default`
                    FROM `cache_attrib`
                    LEFT JOIN `sys_trans` AS `t1`
                        ON `cache_attrib`.`trans_id`=`t1`.`id`
                        AND `cache_attrib`.`name`=`t1`.`text`
                    LEFT JOIN `sys_trans_text` AS `tt1`
                        ON `t1`.`id`=`tt1`.`trans_id`
                        AND `tt1`.`lang`='&1'
                    LEFT JOIN `sys_trans` AS `t2`
                        ON `cache_attrib`.`html_desc_trans_id`=`t2`.`id`
                    LEFT JOIN `sys_trans_text` AS `tt2`
                        ON `t2`.`id`=`tt2`.`trans_id`
                        AND `tt2`.`lang`='&1'
                    WHERE `cache_attrib`.`group_id`='&2'" . $sAddWhereSql . '
                    AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
                    ORDER BY `cache_attrib`.`group_id` ASC',
                    $opt['template']['locale'],
                    $rAttrGroup['id']
                );
            } else {
                $rsAttr = sql(
                    "SELECT `cache_attrib`.`id`,
                            IFNULL(`tt1`.`text`, `cache_attrib`.`name`) AS `name`,
                            IFNULL(`tt2`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`,
                            `cache_attrib`.`icon`, `cache_attrib`.`search_default`
                    FROM `caches_attributes`
                    INNER JOIN `cache_attrib`
                        ON `caches_attributes`.`attrib_id`=`cache_attrib`.`id`
                    LEFT JOIN `sys_trans` AS `t1`
                        ON `cache_attrib`.`trans_id`=`t1`.`id`
                        AND `cache_attrib`.`name`=`t1`.`text`
                    LEFT JOIN `sys_trans_text` AS `tt1`
                        ON `t1`.`id`=`tt1`.`trans_id`
                        AND `tt1`.`lang`='&2'
                    LEFT JOIN `sys_trans` AS `t2`
                        ON `cache_attrib`.`html_desc_trans_id`=`t2`.`id`
                    LEFT JOIN `sys_trans_text` AS `tt2`
                        ON `t2`.`id`=`tt2`.`trans_id`
                        AND `tt2`.`lang`='&2'
                    WHERE `caches_attributes`.`cache_id`='&1'
                    AND `cache_attrib`.`group_id`='&3'
                    AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
                    ORDER BY `cache_attrib`.`group_id` ASC",
                    $cacheId,
                    $opt['template']['locale'],
                    $rAttrGroup['id']
                );
            }
            while ($rAttr = sql_fetch_assoc($rsAttr)) {
                if ($firstLetterUppercase) {
                    $rAttr['name'] = mb_strtoupper(mb_substr($rAttr['name'], 0, 1)) . mb_substr($rAttr['name'], 1);
                }
                $attr[] = $rAttr;
                if ($rAttr['search_default']) {
                    $bSearchGroupDefault = true;
                }
            }
            sql_free_result($rsAttr);

            if (count($attr) > 0) {
                $attributes[] = array(
                    'id' => $rAttrGroup['id'],
                    'name' => $rAttrGroup['name'],
                    'color' => $rAttrGroup['color'],
                    'category' => $rAttrGroup['category'],
                    'search_default' => $bSearchGroupDefault,
                    'attr' => $attr
                );
            }
        }
        sql_free_result($rsAttrGroup);

        return $attributes;
    }

    /**
     * @param $attribId
     * @return array
     */
    public static function getConflictingAttribIds($attribId)
    {
        static $conflicts = [
            [1, 38],    // only at night - 24/7
            [1, 40],    // only at night - only by day
            [24, 25],   // near the parking area - long walk
            [24, 27],   // near the parking area - hilly area
            [24, 29],   // near the parking area - swimming required
            [24, 50],   // near the parking area - cave equipment
            [24, 51],   // near the parking area - diving equipment
            [24, 52],   // near the parking area - watercraft
            [38, 39],   // 24/7 - only at specified times
            [38, 40],   // 24/7 - only by day
            [42, 43],   // all seasons - breeding season
            [42, 60]    // all seassons - only during specified seasons
        ];

        static $conflictsByAttr = [];

        if (!$conflictsByAttr) {
            foreach ($conflicts as $conflict) {
                $conflictsByAttr[$conflict[0]][] = $conflict[1];
                $conflictsByAttr[$conflict[1]][] = $conflict[0];
            }
        }

        return isset($conflictsByAttr[$attribId]) ? $conflictsByAttr[$attribId] : [];
    }
}
