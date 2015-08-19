{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}My profile data{/t}
</div>

	<form action="myprofile.php" method="post" style="display:inline;">
		<input type="hidden" name="action" value="change" />
		<input type="hidden" name="showAllCountries" value="{$showAllCountries}" />

		<table class="table">
			<tr>
				<td colspan="3">
					<span class="boldtext ">{t}The following informations are stored in your userprofile:{/t}</span><br />
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="" align="middle" />
				<span class="systemlink" style="font-size:10px;">{t}Only the <span class="public-setting">green entries</span> are visible in your <a href="viewprofile.php">public profile</a>.{/t}</span>
				</td>
			</tr>
			<tr><td class="spacer"></td></tr>

			<tr>
				<td class="public-setting">{t}Username:{/t}</td>
				<td class="public-setting">
					{if $edit==true}
						<input type="text" name="username" class="public-setting" value="{$username|escape}" maxlength="60" size="30" />
						{if $usernameErrorInvalidChars==true}
							<span class="errormsg">{t}The username is not valid.{/t}</span>
						{elseif $errorUsernameExist==true}
							<span class="errormsg">{t}There already exists an account with that username.{/t}</span>
						{/if}
					{else}
						{$username|escape}
					{/if}
				</td>
			</tr>

			<tr>
				<td>{t}First name:{/t}</td>
				<td>
					{if $edit==true}
						<input type="text" name="firstName" value="{$firstName|escape}" maxlength="60" size="30" />
						{if $firstNameError==true}
							<span class="errormsg">{t}The first name is not valid.{/t}</span>
						{/if}
					{else}
						{$firstName|escape}
					{/if}
				</td>
			</tr>

			<tr>
				<td>{t}Last name:{/t}</td>
				<td>
					{if $edit==true}
						<input type="text" name="lastName" value="{$lastName|escape}" maxlength="60" size="30" />
						{if $lastNameError==true}
							<span class="errormsg">{t}The last name is not valid.{/t}</span>
						{/if}
					{else}
						{$lastName|escape}
					{/if}
				</td>
			</tr>

			<tr>
				<td class="public-setting">{t}Country:{/t}</td>
				<td class="public-setting">
					{if $edit==true}
						<select name="country"class="public-setting">
							<option value="XX" {if $countryCode=="XX"}selected="selected"{/if}>{t}Not defined{/t}</option>
							{foreach from=$countries item=countryItem}
								<option value="{$countryItem.id|escape}" {if $countryCode==$countryItem.id}selected="selected"{/if}>{$countryItem.name|escape}</option>
							{/foreach}
						</select>
						{if $showAllCountries==false}
							&nbsp;&nbsp;<input type="submit" name="showAllCountriesSubmit" value="{t}Show all{/t}" class="formbutton" onclick="submitbutton('showAllCountriesSubmit')" />
						{/if}
						{if $countryError==true}
							<span class="errormsg">{t}The country is not valid.{/t}</span>
						{/if}
					{else}
						{$country|escape}
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td valign=top>{t}Home coordinates:{/t}</td>
				<td>
					{if $edit==true}
						{coordinput prefix="coord" lat=$coordsDecimal.lat lon=$coordsDecimal.lon}
					{else}
						{$coords.lat|escape} {$coords.lon|escape}
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td valign=top >{t}Notifications:{/t}</td>
				<td>
					{if $edit==true}
						{capture name=inputfield}
							<input type="text" name="notifyRadius" maxlength="3" value="{$notifyRadius|escape}" class="input30" />
						{/capture}
						{t 1=$smarty.capture.inputfield}I want to be notified about new Geocaches within an radius of %1 km.{/t}<br />
						<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
						<span style="font-size:10px;">
							{t}The notification radius must be not more than 150 km. To deaktivate notifications, set the radius to 0.{/t}
						</span>
						{if $notifyRadiusError==true}
							<span class="errormsg">{t}The entered radius is not valid.{/t}</span>
						{/if}
						<br />
						<input type="checkbox" name="notifyOconly" value="1" class="checkbox" {if $notifyOconly}checked="checked"{/if} id="notifyOconly" />
						<label for="notifyOconly">{t 1=$oconly_helpstart 2=$oconly_helpend}Also notify about newly marked %1OConly%2 caches.{/t}</label>
					{else}
						{if $notifyRadius>0}
							{t 1=$notifyRadius|escape}Notify about new Geocaches in a radius of %1 km.{/t}
							<br />
							{if $notifyOconly}
								{t 1=$oconly_helpstart 2=$oconly_helpend}Notify about newly marked %1OConly%2 caches.{/t}
							{else}
								{t 1=$oconly_helpstart 2=$oconly_helpend}Do not notify about newly marked %1OConly%2 caches.{/t}
							{/if}
						{else}
							{t}Do not notify about new Geocaches.{/t}
						{/if}
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			
			<tr>
				<td valign="top">{t}Newsletter:{/t}</td>
				<td valign="top">
					{if $edit==true}
						<input type="checkbox" name="accMailing" value="1" {if $accMailing==true}checked="checked"{/if} id="acc_Mailing" class="checkbox" /> 
						<label for="acc_Mailing">{t}Please send me mailings about news and actions on opencaching.de. (max. 2-5 per year){/t}</label>
						<br />
					{else}
						{if $accMailing==true}
							{t}Yes, I want to recieve mailings about news and actions on opencaching.de. (max. 2-5 per year){/t}<br />
						{else}
							{t}No, I dont't want any mailings about news and actions on opencaching.de.{/t}
						{/if}
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			{if $edit || $usePMR || $permanentLogin || $noHTMLEditor || $sendUsermailAddress }
			<tr>
				<td valign="top">{t}Others:{/t}</td>
				<td valign="top">
					{if $edit==true}
						<input type="checkbox" name="usePMR" class="public-setting" value="1" {if $usePMR==true}checked="checked"{/if} id="l_using_pmr" class="checkbox" /> 
						<label for="l_using_pmr" class="public-setting">{t}I'm taking an PMR walkie talkie on channel 2 with me.{/t}</label>
						<br />
					{else}
						{if $usePMR==true}
							<span class="public-setting">{t}I'm taking an PMR walkie talkie on channel 2 with me.{/t}</span><br />
						{/if}
					{/if}
					{if $edit==true}
						<input type="checkbox" name="permanentLogin" value="1" {if $permanentLogin==true}checked="checked"{/if} id="l_using_permanent_login" class="checkbox" />
						<label for="l_using_permanent_login">{t}Don't log me out after 15 minutes inaktivity.{/t}</label><br/>
						<div style="padding-left:25px;">
							<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
							<span style="color:red; font-size:10px; line-height:1.3em">{t}Attention: If you are using this option, don't forget to log out before other persons can use your computer.
							Otherwise, they can use and modify your personal data.{/t}</span>
						</div>
					{else}
						{if $permanentLogin==true}
							{t}Don't log me out after 15 minutes inaktivity.{/t}<br />
						{/if}
					{/if}
					{if $edit==true}
						<input type="checkbox" name="noWysiwygEditor" value="1" {if $noWysiwygEditor==true}checked="checked"{/if} id="l_no_wysiwyg_edit" class="checkbox" /> 
						<label for="l_no_wysiwyg_edit">{t}Use simple HTML editor by default.{/t}</label>
						<br />
					{else}
						{if $noWysiwygEditor}
							{t}Use simple HTML editor by default.{/t}
						{/if}
					{/if}
					{if $edit==true}
						<input type="checkbox" name="sendUsermailAddress" value="1" {if $sendUsermailAddress==true}checked="checked"{/if} id="l_send_usermail_address" class="checkbox" />
						<label for="l_send_usermail_address">{t}Disclose my e-mail address by default when sending e-mails to other users.{/t}</label>
						<br />
					{else}
						{if $sendUsermailAddress}
							{t}Disclose my e-mail address by default when sending e-mails to other users.{/t}
						{/if}
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			{/if}

			<tr>
				<td class="public-setting">{t}Registered since:{/t}</td>
				<td class="public-setting">{$registeredSince|date_format:$opt.format.datelong}</td>
			</tr>

			{if $errorUnknown==true}
				<tr>
					<td colspan="2">
						<span class="errormsg">{t}An unknown error occured.{/t}</span>
					</td>
				</tr>
			{/if}

			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td class="header-small" colspan="2">
					{if $edit==false}
						<input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('change')" />
					{else}
						<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="flashbutton('cancel')" />&nbsp;&nbsp;
						<input type="submit" name="save" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('save')" />
					{/if}
				</td>
			</tr>
		</table>
	</form>

	{if $edit==false}
		<form action="myprofile.php" method="post" style="display:inline;">
			<input type="hidden" name="action" value="changeemail" />
			<table class="table">
				<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
				<tr>
					<td>{t}E-Mail-Address:{/t}</td>
					<td>{$email|escape}</td>
				</tr>
				<tr><td class="spacer" colspan="2"></td></tr>
				<tr>
					<td class="header-small" colspan="2">
						{if $edit==false}
							<input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('change')" />
						{/if}
					</td>
				</tr>
			</table>
		</form>
	{/if}
