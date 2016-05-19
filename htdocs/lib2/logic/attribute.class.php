<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class attribute
{
    /* array with all attributes grouped by attribute group */
    public static function getAttrbutesListArray($firstLetterUppercase = false)
    {
        return self::getAttrbutesListArrayInternal(0, false, $firstLetterUppercase);
    }

    public static function getSelectableAttrbutesListArray($firstLetterUppercase = false)
    {
        return self::getAttrbutesListArrayInternal(0, true, $firstLetterUppercase);
    }

    public static function getAttrbutesListArrayByCacheId($cacheId, $firstLetterUppercase = false)
    {
        return self::getAttrbutesListArrayInternal($cacheId, false, $firstLetterUppercase);
    }

    public static function getAttrbutesListArrayInternal($cacheId, $bOnlySelectable, $firstLetterUppercase)
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
            $bFirst = true;
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
                    WHERE `cache_attrib`.`group_id`='&2'" . $sAddWhereSql . "
                    AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
                    ORDER BY `cache_attrib`.`group_id` ASC",
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
}
