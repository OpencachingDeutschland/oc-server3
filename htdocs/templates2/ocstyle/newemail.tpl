{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Change E-Mail address{/t}" />
	{t}Change E-Mail address{/t}
</div>

{if $codeChanged==true}
	<br /><p>&nbsp;<span class="okmsg">{t}The E-Mail-Address was changed.{/t}</span></p>
{else}

<form action="newemail.php" method="post" style="display: inline;">
	<input type="hidden" name="request" value="1" />

	<table class="table">
		<tr>
			<td><b>{t}Step 1{/t}</b></td>
		</tr>
		<tr>
			<td class="help" colspan="2">
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
				{t}To change your E-Mail address, you have to request a security code. It will be sent to your new E-Mail-Address.{/t}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}New E-Mail address:{/t}</td>
			<td>
				<input name="email" maxlength="60" type="text" value="{$email|escape}" class="input200" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
        <input type="submit" name="erequest" value="{t}Request{/t}" class="formbutton" onclick="submitbutton('erequest')" />
			</td>
		</tr>
		{if $emailErrorSame==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}This E-Mail is already assigned to your account.{/t}</td></tr>
		{elseif $emailErrorInvalid==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}The E-Mail is not valid.{/t}</td></tr>
		{elseif $emailRequested==true}
			<tr><td>&nbsp;</td><td class="successmsg">{t}An E-Mail was sent to you with the security code.{/t}<br />
			{t}If you do not see any E-Mail, please check the spam folder of your mailbox.{/t}</td></tr>
		{elseif $emailErrorUnkown==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}An unknown error occured.{/t}</td></tr>
		{elseif $emailErrorExists==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}There already exists an account with that E-Mail address. It's not possible the register a scond account with the same E-Mail address.{/t}</td></tr>
		{/if}
		<tr><td class="spacer" colspan="2"></td></tr>
	</table>
</form>

<form action="newemail.php" method="post" style="display: inline;">
	<input type="hidden" name="change" value="1" />

	<table class="table">
		<tr>
			<td><b>{t}Step 2{/t}</b></td>
		</tr>
		<tr>
			<td class="help" colspan="2">
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
				{t}Please enter the security code you received. The security code is only 3 days valid. You have to request a new one after that time.{/t}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}New E-Mail address:{/t}</td>
			<td>
				{if $newemail==''}
					<i>&lt;{t}Request security code first.{/t}&gt;</i>
				{else}
					{$newemail|escape}
				{/if}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}Security code:{/t}</td>
			<td>
				<input name="code" maxlength="60" type="text" value="{$code|escape}" class="input100" />
			</td>
		</tr>
		{if $codeErrorExpired==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}The security code is expired. Request a new one.{/t}</td></tr>
		{elseif $codeErrorNoNewEMail==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}There was no new E-Mail-Address entered, please request a security code first.{/t}</td></tr>
		{elseif $codeErrorNotMatch==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}The security code does not match.{/t}</td></tr>
		{elseif $codeErrorEMailExists==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}There already exists an account with that E-Mail address. It's not possible the register a scond account with the same E-Mail address.{/t}</td></tr>
		{elseif $codeErrorUnkown==true}
			<tr><td>&nbsp;</td><td class="errormsg">{t}An unknown error occured.{/t}</td></tr>
		{/if}
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td>&nbsp;</td>
			<td class="header-small" colspan="2">
				<!-- <input type="reset" name="clear" value="{t}Reset{/t}" class="formbutton" onclick="flashbutton('clear')" />&nbsp;&nbsp; -->
				<input type="submit" name="confirm" value="{t}Change{/t}" class="formbutton" onclick="submitbutton('confirm	')" />
			</td>
		</tr>
	</table>
</form>

{/if}
