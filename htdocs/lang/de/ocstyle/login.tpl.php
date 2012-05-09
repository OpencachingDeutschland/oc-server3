<?php
/***************************************************************************
											./lang/de/ocstyle/login.tpl.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
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
                                      				                                
	 login page
	
	 template replacement(s):
	 
	   username      default username
	   message       message to display the user
	   target        page to display after login
	
 ****************************************************************************/
?>

		  <div class="content2-pagetitle"><img src="lang/de/ocstyle/images/profile/32x32-profile.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Login{/t}" />{t}Login{/t}</div>

<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login_form" dir="ltr" style="display: inline;">
<input type="hidden" name="target" value="{target}" />
<input type="hidden" name="action" value="login" />
<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>
	{message_start}<tr><td colspan="2" class="message">{message}</td></tr><tr><td class="spacer" colspan="2"></td></tr>{message_end}
	<tr>
		<td>{t}Username:{/t}</td>
		<td><input name="email" maxlength="80" type="text" class="textboxes" value="{username}" class="input200" /></td>
	</tr>
	<tr>
		<td>{t}Password:{/t}</td>
		<td><input name="password" maxlength="60" type="password" class="textboxes" value="" class="input200" /></td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td class="header-small" colspan="2">
			<input type="reset" name="reset" value="{t}Reset{/t}" class="formbuttons" />&nbsp;&nbsp;
			<input type="submit" name="LogMeIn" value="{t}Login{/t}" class="formbuttons" />
		</td>
	</tr>

</table>
</form>
<div class="content-txtbox-noshade">
	<p style="line-height: 1.6em;">
		{t}Not registered?{/t} <a href="register.php">{t}Register{/t}</a><br />
		{t}Forgotten your password?{/t} <a href="newpw.php">{t}Create a new password{/t}</a><br />
		{t}Forgotten your E-Mail-Address?{/t} <a href="remindemail.php">{t}Remind me{/t}</a><br />
		{t}Here you can find more troubleshooting:{/t} <a href="http://blog.geocaching.de/?page_id=268">{t}Problems with login{/t}</a>.
	</p>
	<div class="buffer" style="width: 500px;">&nbsp;</div>
</div>