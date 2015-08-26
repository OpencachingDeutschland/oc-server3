{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}

<form action="verifyemail.php" method="post" style="display:inline;">

	<input type="hidden" name="page" value="{$orgpage}" />

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" border="0" align="middle" width="32" height="32" alt="" />
		{t}Verify e-mail address{/t}
	</div>

	<br />
	<p>{t}One or more e-mails could not be delivered to the address you specified in your user profile:{/t}</p>
	<p><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>{$emailadr}</strong></p>
	<p><br />{t}Please confirm that this email address is correct, or enter a new one.{/t}</p>
	<br />

	<input type="submit" name="new" value="{t}Enter new email address{/t}" class="formbutton" style="width:200px" onclick="submitbutton('new')" />&nbsp;&nbsp;
	<input type="submit" name="confirm" value="{t}Confirm this email address{/t}" class="formbutton" style="width:200px" onclick="submitbutton('confirm')" />
	<br />

	{$datalicensemail}

</form>
