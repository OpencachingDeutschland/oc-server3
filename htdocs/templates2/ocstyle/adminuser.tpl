{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
	{t}View useraccount details{/t}
</div>

<form method="post" action="adminuser.php">
	<input type="hidden" name="action" value="searchuser" />
		<p style="line-height: 1.6em;"><strong>{t}Username or email address:{/t}</strong> &nbsp;<input type="text" name="username" size="30" value="{$username|escape}" /></p>
		
		{if $error=='userunknown'}
			<p style="line-height: 1.6em; color: red; font-weight: bold;">{t}Username unknown{/t}</p>
		{/if}
		{if $success=='1'}
			<p style="line-height: 1.6em; color: green; font-weight: bold;">{t}User status was successfully changed.{/t}</p>		
		{/if}
		
		<p style="line-height: 1.6em;"><input type="submit" name="find" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('find')" /></p>
		
</form>

{if $showdetails==true}
	<form method="post" action="adminuser.php">
		<input type="hidden" name="action" value="formaction" />
		<input type="hidden" name="userid" value="{$user.user_id|escape}" />

		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
			{t}Useraccount details{/t}
		</div>

		<table class="narrowtable">
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
				<td>{t}Last modified{/t}{t}#colonspace#{/t}:</td>
				<td>{$user.last_modified|date_format:$opt.format.date}</td>
			</tr>
			<tr>
				<td>{t}User active:{/t}</td>
				<td>{if $user.is_active_flag}{t}Yes{/t}{else}{t}No{/t}{/if}
						{if $user.license_declined}&nbsp;({t}declined data license{/t}){/if}</td>
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
				<td>{t}Log entries:{/t}</td>
				<td>{$user.logentries}
				    {if $user.deleted_logentries > 0}&nbsp;(+ {$user.deleted_logentries} {t}deleted{/t} / {t}archived{/t}){/if}
				</td>
			</tr>
			<tr>
				<td>{t}Total hidden:{/t}</td>
				<td>{$user.hidden|escape}</td>
			</tr>
			<tr>
				<td>{t}Active geocaches:{/t}</td>
				<td>{$user.hidden_active|escape} &nbsp; &ndash; &nbsp; <a href="ownerlogs.php?userid={$user.user_id|escape}">{t}Show log history{/t}</a></td>
			</tr>
			<tr>
				<td>{t}Cache reports{/t}{t}#colonspace#{/t}:</td>
				<td>{$user.reports|escape}</td>
			</tr>
			<tr>
				<td>{t}Last known login:{/t}</td>
				<td>{if $user.last_known_login}{$user.last_known_login|date_format:$opt.format.date}{else}{t}Unknown{/t}{/if}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>

			<tr><td class="spacer" colspan="2"></td></tr>

			{if $licensefunctions==true}
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="chkdisduelicense" value="1" /> {t}Disable (and lock all geocaches owned) and remove all foreign licensed content{/t}</td>
				</tr>
			{/if}

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
					<td><input type="checkbox" name="chkemail" value="1" /> {t}Mark e-mail address as invalid{/t}{if $licensefunctions}&nbsp;&nbsp;&nbsp;<input type="checkbox" name="chkdl" value="1" /> {t}data license mail was not delivered{/t}{/if}</td>
				</tr>
			{/if}

			{if $licensefunctions==true || $candelete==true || $candisable==true || $cansetemail==true}
				<tr>
					<td>&nbsp;</td>
					<td><input type="checkbox" name="chkcommit" value="1" /> {t}Sure?{/t}</td>
				</tr>
				<tr><td class="spacer" colspan="2"></td></tr>

				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" name="execute" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('execute')" /></td>
				</tr>
			{/if} 
		</table>
	</form>
{/if}
