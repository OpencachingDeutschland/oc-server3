{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="World" />
	{t}View useraccount details{/t}
</div>

<form method="post" action="adminuser.php">
	<input type="hidden" name="action" value="searchuser" />
		<p style="line-height: 1.6em;"><strong>{t}Username:{/t}</strong> <input type="text" name="username" size="30" value="{$username|escape}" /></p>
		
		{if $error=='userunknown'}
			<p style="line-height: 1.6em; color: red; font-weight: bold;">{t}Username unknown{/t}</p>
		{/if}
		{if $success=='1'}
			<p style="line-height: 1.6em; color: green; font-weight: bold;">{t}User status was successfully changed.{/t}</p>		
		{/if}
		
		<p style="line-height: 1.6em;"><input type="submit" value="{t}Submit{/t}" /></p>
		
</form>

{if $showdetails==true}
	<form method="post" action="adminuser.php">
		<input type="hidden" name="action" value="formaction" />
		<input type="hidden" name="userid" value="{$user.user_id|escape}" />

		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="World" />
			{t}Useraccount details{/t}
		</div>

		<table class="content">
			<tr>
				<td>{t}Username:{/t}</td>
				<td><a href="viewprofile.php?userid={$user.user_id|escape}" target="_blank">{$user.username|escape}</a></td>
			</tr>
			<tr>
				<td>{t}User-ID:{/t}</td>
				<td><a href="viewprofile.php?userid={$user.user_id|escape}" target="_blank">{$user.user_id|escape}</a></td>
			</tr>
			<tr>
				<td>{t}E-Mail:{/t}</td>
				<td>{if $user.email_problems > 0}<span class="errormsg">{$user.email|escape}</span>{else}{$user.email|escape}{/if}</td>
			</tr>
			<tr>
				<td>{t}Date created:{/t}</td>
				<td>{$user.date_created|date_format:$opt.format.date}</td>
			</tr>
			<tr>
				<td>{t}Last modified:{/t}</td>
				<td>{$user.last_modified|date_format:$opt.format.date}</td>
			</tr>
			<tr>
				<td>{t}User active:{/t}</td>
				<td>{if $user.is_active_flag}{t}Yes{/t}{else}{t}No{/t}{/if}</td>
			</tr>
			<tr>
				<td>{t}Activation code:{/t}</td>
				<td>
					{$user.activation_code|escape}
					{if $user.activation_code!=''}
						<a href="adminuser.php?action=sendcode&userid={$user.user_id|escape}">Code erneut senden</a>
					{/if}
				</td>
			</tr>
			{if $msg=='sendcodecommit'}
				<tr>
					<td>&nbsp;</td>
					<td><span class="errormsg">{t}A new activation mail has been sent.{/t}</class></td>
				</tr>
			{/if}
			<tr>
				<td>{t}First name:{/t}</td>
				<td>{$user.first_name|escape}</td>
			</tr>
			<tr>
				<td>{t}Last name:{/t}</td>
				<td>{$user.last_name|escape}</td>
			</tr>
			<tr>
				<td>{t}Logentries:{/t}</td>
				<td>{$user.logentries|escape}</td>
			</tr>
			<tr>
				<td>{t}Total hidden:{/t}</td>
				<td>{$user.hidden|escape}</td>
			</tr>
			<tr>
				<td>{t}Active geocaches:{/t}</td>
				<td>{$user.hidden_active|escape}</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td>{t}Last known login:{/t}</td>
				<td>{if $user.last_known_login}{$user.last_known_login|date_format:$opt.format.date}{else}{t}Unknown{/t}{/if}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			  <td>
					{t}* If the user explicitly logged out after page visit or last login <br />
					is older than 90 days, the last login cannot be determined.{/t}
			  </td>
			</tr>

			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>&nbsp;</td>
				<td><input type="checkbox" name="chkdisduelicense" value="1" /> {t}Disable (and lock all geocaches owned) and remove all foreign licensed content{/t}</td>
			</tr>

			{if $candisable==true}
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="chkdisable" value="1" /> {t}Disable (and lock all geocaches owned){/t}</td>
				</tr>
			{/if}
			{if $candelete==true}
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="chkdelete" value="1" /> {t}Delete{/t}</td>
				</tr>
			{/if}
			{if $cansetemail==true}
				<tr>
					<td></td>
					<td><input type="checkbox" name="chkemail" value="1"> {t}Mark e-mail address as invalid{/t}&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkdl" value="1"> {t}data license mail was not delivered{/t}</td>
				</tr>
			{/if}

{*			{if $candelete==true || $candisable==true} *}
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="chkcommit" value="1" /> {t}Sure?{/t}</td>
				</tr>
				<tr><td class="spacer" colspan="2"></td></tr>

				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="{t}Submit{/t}" /></td>
				</tr>
{*			{/if} *}
		</table>
	</form>
{/if}
