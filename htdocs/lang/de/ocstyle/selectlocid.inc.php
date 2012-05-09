<?php
/***************************************************************************
												  ./lang/de/ocstyle/selectlocid.inc.php
															-------------------
		begin                : October 29 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************
	     
   Unicode Reminder メモ
                                    				                                
	 set template specific language variables
	
 ****************************************************************************/

	$locline = '<tr><td width="50px"><p>{nr}.&nbsp;</p></td><td><p><b><a href="search.php?{urlparams}">{locationname}</a>{secondlocationname}</b></p></td></tr>
							<tr><td width="50px">&nbsp;</td><td><p>{coords}</p></td></tr>
							<tr><td width="50px">&nbsp;</td><td style="padding-bottom:3px;"><span style="color:#001BBC">{parentlocations}</span></td></tr>';
	
	$secondlocationname = '&nbsp;<font size="1">({secondlocationname})</font>';
	
	function landFromLocid($locid)
	{
		global $dblink;

		if (!is_numeric($locid)) return '';
		$locid = $locid + 0;

		$rs = sql("SELECT `ld`.`text_val` `land` FROM `geodb_textdata` `ct`, `geodb_textdata` `ld`, `geodb_hierarchies` `hr` WHERE `ct`.`loc_id`=`hr`.`loc_id` AND `hr`.`id_lvl2`=`ld`.`loc_id` AND `ct`.`text_type`=500100000 AND `ld`.`text_locale`='DE' AND `ld`.`text_type`=500100000 AND `ct`.`loc_id`='&1' AND `hr`.`id_lvl2`!=0", $locid);
		if ($r = sql_fetch_array($rs))
			return $r['land'];
		else
			return 0;
	}

	function regierungsbezirkFromLocid($locid)
	{
		global $dblink;

		if (!is_numeric($locid)) return '';
		$locid = $locid + 0;

		$rs = sql("SELECT `rb`.`text_val` `regierungsbezirk` FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr` WHERE `ct`.`loc_id`=`hr`.`loc_id` AND `hr`.`id_lvl4`=`rb`.`loc_id` AND `ct`.`text_type`=500100000 AND `rb`.`text_type`=500100000 AND `ct`.`loc_id`='&1' AND `hr`.`id_lvl4`!=0", $locid);
		if ($r = sql_fetch_array($rs))
			return $r['regierungsbezirk'];
		else
			return 0;
	}

	function landkreisFromLocid($locid)
	{
		global $dblink;

		if (!is_numeric($locid)) return '';
		$locid = $locid + 0;

		$rs = sql("SELECT `rb`.`text_val` `regierungsbezirk` FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr` WHERE `ct`.`loc_id`=`hr`.`loc_id` AND `hr`.`id_lvl5`=`rb`.`loc_id` AND `ct`.`text_type`=500100000 AND `rb`.`text_type`=500100000 AND `ct`.`loc_id`='&1' AND `hr`.`id_lvl5`!=0", $locid);
		if ($r = sql_fetch_array($rs))
			return $r['regierungsbezirk'];
		else
			return 0;
	}
?>