<?php 
/****************************************************************************
											./lang/de/ocstyle/error.tpl.php
															-------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	  
   Unicode Reminder メモ
                                       				                                
	 Critical error messages for the user
	
	 template replacement(s):
	 
	   tplname       Name of the template in which the error occurs
	   error_msg     message to display the user
	
 ****************************************************************************/
?>
		  <div class="content2-pagetitle"><img src="resource2/ocstyle/images/misc/32x32-gears.png" style="margin-right: 10px;" width="32" height="32" />{t}error while loading the page{/t}</div>
<p>{t}In an attempt to create the page, an error has occurred. If you pass through a hyperlink on our side and the problem persists do not hesitate to contact us by email.{/t}</p>
<p style="font-size:x-small;margin-bottom:0px;margin-left:15px;">{t}The following error occurred:{/t}</p>
<p style="margin-top:0px;margin-left:15px;margin-right:20px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;">
{t}Page:{/t} {tplname}<br/>
{t}Error message{/t}{t}#colonspace#{/t}: {error_msg}
</p>