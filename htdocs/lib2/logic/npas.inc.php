<?php
/****************************************************************************

		For license information see doc/license.txt

		Unicode Reminder メモ

		Nature Protection Area functions

 ****************************************************************************/


	function get_npas($cache_id)
	{
		$rsNPA = sql(
	          "SELECT `npa_areas`.`name` AS `npaName`, `npa_types`.`name` AS `npaTypeName`
	             FROM `cache_npa_areas`
	       INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id`
	       INNER JOIN `npa_types` ON `npa_areas`.`type_id`=`npa_types`.`id`
	            WHERE `cache_npa_areas`.`cache_id`='&1'
	         GROUP BY `npa_areas`.`type_id`, `npa_areas`.`name`
	         ORDER BY `npa_types`.`ordinal` ASC",
	                  $cache_id);
		$npas = array();
		while ($rNPA = sql_fetch_array($rsNPA))
			$npas[] = $rNPA;
		sql_free_result($rsNPA);

		return $npas;
	}


	function get_desc_npas($cache_id)
	{
		global $opt;

		$npas = get_npas($cache_id);
		if ($npas)
		{
			$desc = "<p>" . str_replace('%1',helppagelink('npa'), _('This geocache is probably placed within the following protected areas (%1Info</a>):')) . "</p>\n" .
			        "<ul>\n";
			foreach ($npas as $npa)
				$desc .= "<li>" . $npa['npaTypeName'] . ": <a href='http://www.google.de/search?q=".urlencode($npa['npaTypeName'].' '.$npa['npaName'])."' target='_blank'>" . $npa['npaName'] . "</a></li>\n";
			$desc .= "</ul>\n";
		}
		else
			$desc = "";

		return $desc;
	}

?>