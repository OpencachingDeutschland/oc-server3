<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class attribute
{
	/* array with all attributes grouped by attribute group */
	static function getAttrbutesListArray()
	{
		return attribute::getAttrbutesListArrayInternal(0, false);
	}

	static function getSelectableAttrbutesListArray()
	{
		return attribute::getAttrbutesListArrayInternal(0, true);
	}

	static function getAttrbutesListArrayByCacheId($cacheId)
	{
		return attribute::getAttrbutesListArrayInternal($cacheId, false);
	}

	static function getAttrbutesListArrayInternal($cacheId, $bOnlySelectable)
	{
		global $opt;

		$attributes = array();
		$rsAttrGroup = sql("SELECT `attribute_groups`.`id`, 
		                           IFNULL(`tt1`.`text`, `attribute_groups`.`name`) AS `name`, 
		                           IFNULL(`tt2`.`text`, `attribute_categories`.`name`) AS `category`, 
		                           `attribute_categories`.`color`
													FROM `attribute_groups` 
										INNER JOIN `attribute_categories` ON `attribute_groups`.`category_id`=`attribute_categories`.`id`
										 LEFT JOIN `sys_trans` AS `t1` ON `attribute_groups`.`trans_id`=`t1`.`id` AND `attribute_groups`.`name`=`t1`.`text` 
										 LEFT JOIN `sys_trans_text` AS `tt1` ON `t1`.`id`=`tt1`.`trans_id` AND `tt1`.`lang`='&1' 
										 LEFT JOIN `sys_trans` AS `t2` ON `attribute_categories`.`trans_id`=`t2`.`id` AND `attribute_categories`.`name`=`t2`.`text` 
										 LEFT JOIN `sys_trans_text` AS `tt2` ON `t2`.`id`=`tt2`.`trans_id` AND `tt2`.`lang`='&1' 
											ORDER BY `attribute_groups`.`id` ASC", $opt['template']['locale']);
		while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup))
		{
			$attr = array();
			$bFirst = true;
			
			if ($cacheId == 0)
			{
				$sAddWhereSql = '';
				if ($bOnlySelectable == true)
					$sAddWhereSql = ' AND `cache_attrib`.`selectable`=1';

				$rsAttr = sql("SELECT `cache_attrib`.`id`, IFNULL(`tt1`.`text`, `cache_attrib`.`name`) AS `name`,
															IFNULL(`tt2`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`, `cache_attrib`.`icon`
												 FROM `cache_attrib`
										LEFT JOIN `sys_trans` AS `t1` ON `cache_attrib`.`trans_id`=`t1`.`id` AND `cache_attrib`.`name`=`t1`.`text` 
										LEFT JOIN `sys_trans_text` AS `tt1` ON `t1`.`id`=`tt1`.`trans_id` AND `tt1`.`lang`='&1' 
										LEFT JOIN `sys_trans` AS `t2` ON `cache_attrib`.`html_desc_trans_id`=`t2`.`id` 
										LEFT JOIN `sys_trans_text` AS `tt2` ON `t2`.`id`=`tt2`.`trans_id` AND `tt2`.`lang`='&1' 
												WHERE `cache_attrib`.`group_id`='&2'
												AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
										 ORDER BY `cache_attrib`.`group_id` ASC", $opt['template']['locale'], $rAttrGroup['id']);
			}
			else
			{
				$rsAttr = sql("SELECT `cache_attrib`.`id`, IFNULL(`tt1`.`text`, `cache_attrib`.`name`) AS `name`,
															IFNULL(`tt2`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`, `cache_attrib`.`icon`
												 FROM `caches_attributes` 
									 INNER JOIN `cache_attrib` ON `caches_attributes`.`attrib_id`=`cache_attrib`.`id` 
										LEFT JOIN `sys_trans` AS `t1` ON `cache_attrib`.`trans_id`=`t1`.`id` AND `cache_attrib`.`name`=`t1`.`text` 
										LEFT JOIN `sys_trans_text` AS `tt1` ON `t1`.`id`=`tt1`.`trans_id` AND `tt1`.`lang`='&2' 
										LEFT JOIN `sys_trans` AS `t2` ON `cache_attrib`.`html_desc_trans_id`=`t2`.`id` 
										LEFT JOIN `sys_trans_text` AS `tt2` ON `t2`.`id`=`tt2`.`trans_id` AND `tt2`.`lang`='&2' 
												WHERE `caches_attributes`.`cache_id`='&1' AND `cache_attrib`.`group_id`='&3'
												AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
										 ORDER BY `cache_attrib`.`group_id` ASC", $cacheId, $opt['template']['locale'], $rAttrGroup['id']);
			}
			while ($rAttr = sql_fetch_assoc($rsAttr))
				$attr[] = $rAttr;
			sql_free_result($rsAttr);

			if (count($attr) > 0)
				$attributes[] = array('name' => $rAttrGroup['name'], 
															'color' => $rAttrGroup['color'], 
															'category' => $rAttrGroup['category'],
															'attr' => $attr);
		}
		sql_free_result($rsAttrGroup);

		return $attributes;
	}
}
?>