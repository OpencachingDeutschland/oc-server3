<?php
/****************************************************************************
											./lang/de/ocstyle/removelogs.tpl.php
															-------------------
		begin                : July 9 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	  
   Unicode Reminder メモ
                                       				                                
	 remove a cache log
		
		cachename
		logid_urlencode
		log

 ****************************************************************************/
?>
<div class="content2-pagetitle"><img src="lang/de/ocstyle/images/description/22x22-logs.png" style="margin-right: 10px;" width="22" height="22"/>{t}remove log entry for <a href="viewcache.php?cacheid={cacheid}">{cachename}</a>{/t}</div>
<form action="removelog.php" method="post" enctype="application/x-www-form-urlencoded" name="removelog_form" dir="ltr">
<input type="hidden" name="commit" value="1"/>
<input type="hidden" name="logid" value="{logid}"/>
<table class="table">
	<tr><td class="spacer"></td></tr>

	<tr><td colspan="2">{t}Are you sure to remove your log entry?{/t}</td></tr>
	<tr><td class="spacer"></td></tr>

	<tr>
		<td>&nbsp;&nbsp;&nbsp;</td>
		<td>
			<div class="textblock">
				<p>{logimage} {date}{time} &nbsp; {typetext}</p>
				<p>{logtext}</p>
			</div>
		</td>
	</tr>
	<tr><td class="spacer">&nbsp;</td></tr>

	<tr>
		<td class="header-small" colspan="2">
			<input type="submit" name="submit" value="{t}Remove log entry{/t}"  class="formbutton" style="width: 150px;" onclick="submitbutton('submit')" />
		</td>
	</tr>
</table>
</form>
