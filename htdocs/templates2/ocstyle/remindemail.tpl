{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<form action="remindemail.php" method="post">
  <div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Remind my account E-Mail{/t}" />
		{t}Remind my account E-Mail{/t}
	</div>

	<table class="table">
		<tr>
			<td class="help" colspan="2">
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" alt="" />
				{t}Enter your username and we will send an E-Mail to your E-Mail-Address.{/t}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}Username:{/t}</td>
			<td>
				<input name="username" type="text" value="{$username|escape}" maglength="60" class="input200"  />
			</td>
		</tr>

		{if $errorUsernameInvalid==true}
			<tr><td colspan="2" class="errormsg">{t}Enter an username.{/t}</td></tr>
		{elseif $errorUsernameNotExist==true}
			<tr><td colspan="2" class="errormsg">{t}Username does not exist.{/t}</td></tr>
		{elseif $errorUnkown==true}
			<tr><td colspan="2" class="errormsg">{t}An unkown error occured.{/t}</td></tr>
		{elseif $remindMailSent==true}
			<tr><td colspan="2" class="successmsg">{t}The remind E-Mail was sent.{/t}<br />
			{t}If you do not see any E-Mail, please check the spam folder of your mailbox.{/t}</td></tr>
		{/if}

		<tr><td class="spacer" colspan="2"></td></tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td width="150px">&nbsp;</td>
			<td>
				<input type="submit" name="ok" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('ok')" />
			</td>
		</tr>
	</table>
</form>
