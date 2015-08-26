{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{if $confirm==1}
	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Register{/t}" />
		{t}New user registered{/t}
	</div>

	<p>
		<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Note{/t}" title="{t}Note{/t}" align="middle" />
		{t}The activation code was sent with an E-Mail to you.<br />
		Please follow the instructions in the E-Mail.{/t}<br />
		{t}If you do not see any E-Mail, please check the spam folder of your mailbox.{/t}
	</p>

	<table class="table">
		<tr>
			<td>{t}Username:{/t}</td>
			<td>{$username|escape}</td>
		</tr>

		<tr>
			<td>{t}E-Mail:{/t}</td>
			<td>{$email|escape}</td>
		</tr>

		<tr>
			<td>{t}First name:{/t}</td>
			<td>{$first_name|escape}</td>
		</tr>

		<tr>
			<td>{t}Last name:{/t}</td>
			<td>{$last_name|escape}</td>
		</tr>

		<tr>
			<td>{t}Country:{/t}</td>
			<td>{$country_full|escape}</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
	</table>

{else}

	<form name="register" action="register.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;">
		<input type="hidden" name="show_all_countries" value="{$show_all_countries}" />
		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Register{/t}" />
			{t}Register new user{/t}
		</div>

		<div class="article">
		<table class="table">
			<tr>
				<td colspan="2" class="help">
					<p>
						<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Note{/t}" align="middle" />
						{t}To create an account on Opencaching.de, you have to enter a vaild E-Mail address and an username.
						An E-Mail will be sent to the address you supplied with an activation code.{/t}
					</p>
					<p>
						{t}Only one account can be created per E-Mail address. First name, last name and country are optional.{/t}
					</p>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td width="145" valign="top">{t}Username:{/t}</td>
				<td valign="top">
					<input type="text" name="username" maxlength="60" value="{$username|escape}" class="input200" /> *
					{if $error_username_not_ok==1}
						<span class="errormsg">{t}The username is not valid.{/t}</span>
					{elseif $error_username_exists==1}
						<span class="errormsg">{t}There already exists an account with that username.{/t}</span>
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"><div class="mediumspacer"></td></tr>

			<tr>
				<td width="145" valign="top">{t}First name:{/t}</td>
				<td valign="top">
					<input type="text" name="first_name" maxlength="60" value="{$first_name|escape}" class="input200" /> 
					{if $error_first_name_not_ok==1}
						<span class="errormsg">{t}The first name is not valid.{/t}</span>
					{/if}
				</td>
			</tr>
			<tr>
				<td width="145" valign="top">{t}Last name:{/t}</td>
				<td valign="top">
					<input type="text" name="last_name" maxlength="60" value="{$last_name|escape}" class="input200" />
					{if $error_last_name_not_ok==1}
						<span class="errormsg">{t}The last name is not valid.{/t}</span>
					{/if}
				</td>
			</tr>
			<tr>
				<td valign="top">{t}Country:{/t}</td>
				<td valign="top">
					<select name="country" class="input200" >
						<option value="XX" {if $country=="XX"}selected="selected"{/if}>{t}Not specified{/t}</option>
						{foreach from=$countries item=countryItem}
							<option value="{$countryItem.id}" {if $countryItem.id==$country}selected="selected"{/if}>{$countryItem.name|escape}</option>
						{/foreach}
					</select>&nbsp;&nbsp;
					{if $show_all_countries==0}
						<input type="submit" name="show_all_countries_submit" value="{t}Show all{/t}" class="formbutton" onclick="submitbutton('show_all_countries_submit')" />
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"><div class="mediumspacer"></td></tr>

			<tr>
				<td width="145" valign="top">{t}E-Mail:{/t}</td>
				<td valign="top">
					<input type="text" name="email" maxlength="80" value="{$email|escape}" class="input200" /> *&nbsp;
					{if $error_email_not_ok==1}
						<span class="errormsg">{t}The E-Mail is not valid.{/t}</span>
					{elseif $error_email_exists==1}
						<span class="errormsg">{t}There already exists an account with that E-Mail address. It's not possible the register
						a scond account with the same E-Mail address.{/t}</span>
					{/if}
				</td>
			</tr>
			<tr>
				<td width="145" valign="top">{t}Your password:{/t}</td>
				<td valign="top">
					<input type="password" name="password1" maxlength="80" value="" class="input200" /> *&nbsp;
				</td>
			</tr>
			<tr>
				<td width="145" valign="top">{t}Please repeat:{/t}</td>
				<td valign="top">
					<input type="password" name="password2" maxlength="80" value="" class="input200" /> *
				</td>
			</tr>
			{if $error_password_not_ok==1 or $error_password_diffs==1}
				<tr>
					<td>&nbsp;</td>
					<td>
						{if $error_password_not_ok==1}
							{include file="res_passworderror.tpl"}
						{elseif $error_password_diffs==1}
							<span class="errormsg">{t}The passwords do not match.{/t}</span>
						{/if}
					</td>
				</tr>
			{/if}
			<tr>
				<td width="145" valign="top">&nbsp;</td>
				<td valign="top">
					{t}* mandatory field{/t}
					{if $error_unkown==1}
						<br />
						<span class="errormsg">
							{t 1=$opt.mail.contact}The account could not be created, the reason is not known.
							If you cannot solve this problem yourself, pleas contact us via <a href="mailto:%1">E-Mail</a>{/t}
						</span>
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td colspan="2" class="help">
					<p class="article">
						<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Note{/t}" title="{t}Note{/t}" align="middle" />
						{t 1=$opt.mail.contact}We respect your privacy. Your personal data will be stored in our database, but not shared to third parties.
						Please note our exact <a href="articles.php?page=dsb">privacy statement</a>. If you have further questions, please <a href="mailto:%1">contact us</a> <b>before</b> you create your
						account.{/t}
					</p>
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<input type="checkbox" name="TOS" value="ON" style="border:0;" />
						{t}I've  read and understand the <a target="_blank" href="articles.php?page=dsb">privacy statement</a>, the <a target="_blank" href="articles.php?page=impressum#tos">terms of service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Datalicense</a> and accept them.{/t}
					{if $error_tos_not_ok==1}
						<br /><span class="errormsg">{t}You have to accept the privacy statement, terms of service and the datalicense to register at opencaching.de{/t}</span>
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td class="header-small" colspan="2">
					<!-- <input type="reset" name="reset" value="{t}Reset{/t}" class="formbutton" onclick="flashbutton('reset')"/>&nbsp;&nbsp; -->
					<input type="submit" name="submit" value="{t}Register{/t}" class="formbutton" onclick="submitbutton('submit')"/>
				</td>
			</tr>
		</table>
		</div>
	</form>
{/if}
