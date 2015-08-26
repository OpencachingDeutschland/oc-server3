<?php
/****************************************************************************
											./lang/de/ocstyle/login.tpl.php
															-------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	   
   Unicode Reminder メモ
                                      				                                
	 login page
	
	 template replacement(s):
	 
	   username      default username
	   message       message to display the user
	   target        page to display after login

	 Is called from lib1 pages when login is required, e.g. newcache.php.

 ****************************************************************************/
?>

<div class="content2-pagetitle"><img src="lang/de/ocstyle/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Login{/t}" />{t}Login{/t}</div>

<form action="login.php" method="post" enctype="application/x-www-form-urlencoded" name="login_form" dir="ltr" style="display: inline;">
<input type="hidden" name="target" value="{target}" />
<input type="hidden" name="action" value="login" />
<input type="hidden" name="source" value="loginpage" />

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>
	{message_start}<tr><td colspan="2" class="message">{message}</td></tr><tr><td class="spacer" colspan="2"></td></tr>{message_end}
	<tr>
		<td>{t}Username:{/t}</td>
		<td><input name="email" maxlength="80" type="text"  value="{username}" class="input200" /></td>
	</tr>
	<tr>
		<td>{t}Password:{/t}</td>
		<td><input name="password" maxlength="60" type="password"  value="" class="input200" /></td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td></td>
		<td class="header-small">
			<input type="submit" name="LogMeIn" value="{t}Login{/t}" class="formbutton" />
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
</table>

</form>

<div class="content-txtbox-noshade systemlink">
	<p style="line-height: 1.6em;">
		<br />
		{t}Not registered?{/t} &nbsp;&rarr;&nbsp; <a href="register.php">{t}Register{/t}</a><br />
		{t}Forgotten your password?{/t} &nbsp;&rarr;&nbsp; <a href="newpw.php">{t}Create a new password{/t}</a><br />
		{t}Forgotten your E-Mail-Address?{/t} &nbsp;&rarr;&nbsp; <a href="remindemail.php">{t}Remind me{/t}</a>
	</p>
	<p>
		{t}Here you can find more troubleshooting:{/t} {helplink}{t}Problems with login{/t}</a>.
	</p>
	<div class="buffer" style="width: 500px;">&nbsp;</div>
</div>