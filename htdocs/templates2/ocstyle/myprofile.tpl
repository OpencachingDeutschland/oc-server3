{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="" />
	{t}My profile data{/t}
</div>

{if $edit==true}
	<form action="myprofile.php" method="post" style="display:inline;">
		<input type="hidden" name="action" value="change" />
		<input type="hidden" name="showAllCountries" value="{$showAllCountries}" />
{/if}

		<div class="content-txtbox-noshade">
			<p style="line-height: 1.6em;">
				{t}The following informations are stored in your userprofile:{/t}<br />
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="" align="middle" />
				<span style="color:#666666; font-size:10px;">{t}Gray entries will be hidden to other users.{/t}</span>
			</p>
		</div>

		<table class="table">
			<tr>
				<td>{t}Username:{/t}</td>
				<td>
					{if $edit==true}
						<input type="text" name="username" value="{$username|escape}" maxlength="60" size="30" />
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
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>{t}E-Mail-Address:{/t}</td>
				<td><font color="#666666">{$email|escape}</font></td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>{t}First name:{/t}</td>
				<td>
					<font color="#666666">
						{if $edit==true}
							<input type="text" name="firstName" value="{$firstName|escape}" maxlength="60" size="30" />
							{if $firstNameError==true}
								<span class="errormsg">{t}The first name is not valid.{/t}</span>
							{/if}
						{else}
							{$firstName|escape}
						{/if}
					</font>
				</td>
			</tr>

			<tr>
				<td>{t}Last name:{/t}</td>
				<td>
					<font color="#666666">
						{if $edit==true}
							<input type="text" name="lastName" value="{$lastName|escape}" maxlength="60" size="30" />
							{if $lastNameError==true}
								<span class="errormsg">{t}The last name is not valid.{/t}</span>
							{/if}
						{else}
							{$lastName|escape}
						{/if}
					</font>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>{t}Country:{/t}</td>
				<td>
					{if $edit==true}
						<select name="country">
							<option value="XX" {if $countryCode=="XX"}selected="selected"{/if}>{t}Not defined{/t}</option>
							{foreach from=$countries item=countryItem}
								<option value="{$countryItem.id|escape}" {if $countryCode==$countryItem.id}selected="selected"{/if}>{$countryItem.name|escape}</option>
							{/foreach}
						</select>
						{if $showAllCountries==false}
							<input type="submit" name="showAllCountriesSubmit" value="{t}Show all{/t}" />
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
					<font color="#666666">
						{if $edit==true}
							{coordinput prefix="coord" lat=$coordsDecimal.lat lon=$coordsDecimal.lon}
						{else}
							{$coords.lat|escape} {$coords.lon|escape}
						{/if}
					</font>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td valign=top>{t}Notifications:{/t}</td>
				<td>
					<font color="#666666">
						{if $edit==true}
							{capture name=inputfield}
								<input type="text" name="notifyRadius" maxlength="3" value="{$notifyRadius|escape}" class="input30" />
							{/capture}
							{t 1=$smarty.capture.inputfield}I want to be notified about new Geocaches within an radius of %1 km.{/t}<br />
							<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle">
							<span style="color:#666666; font-size:10px;">
								{t}The notification radius must be not more than 150 km. To deaktivate notifications, set the radius to 0.{/t}
							</span>
							{if $notifyRadiusError==true}
								<span class="errormsg">{t}The entered radius is not valid.{/t}</span>
							{/if}
						{else}
							{if $notifyRadius>0}
								{t 1=$notifyRadius|escape}Notify about new Geocaches in a radius of %1 km.{/t}
							{else}
								{t}Notification about new Geocaches is not activated.{/t}
							{/if}
						{/if}
					</font>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td valign="top">{t}Others:{/t}</td>
				<td valign="top">
					{if $edit==true}
						<input type="checkbox" name="usePMR" value="1" {if $usePMR==true}checked="checked"{/if} id="l_using_pmr" class="checkbox" /> 
						<label for="l_using_pmr">{t}I'm taking an PMR walkie talkie on channel 2 with me.{/t}</label>
						<br />
					{else}
						{if $usePMR==true}
							<li>{t}I'm taking an PMR walkie talkie on channel 2 with me.{/t}</li>
						{/if}
					{/if}
					{if $edit==true}
						<input type="checkbox" name="permanentLogin" value="1" {if $permanentLogin==true}checked="checked"{/if} id="l_using_permanent_login" class="checkbox" />
						<label for="l_using_permanent_login"><font color="#666666">{t}Don't log me out after 15 minutes inaktivity.{/t}</font></label><br/>
						<div style="padding-left:25px;">
							<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle">
							<font style="color:#FF0000; font-size:10px;">
								{t}Attention: If you are using this option, don't forget to log out before other persons can use your computer.
								Otherwise, they can use and modify your personal data.{/t}
							</font>
						</div>
					{else}
						{if $permanentLogin==true}
							<li><font color="#666666">{t}Don't log me out after 15 minutes inaktivity.{/t}</font></li>
						{/if}
					{/if}
					{if $edit==true}
						<input type="checkbox" name="noHTMLEditor" value="1" {if $noHTMLEditor==true}checked="checked"{/if} id="l_no_htmledit" class="checkbox" /> 
						<label for="l_no_htmledit"><font color="#666666">{t}Don't use an HTML editor by default.{/t}</font></label>
						<br />
					{else}
						{if $useHTMLEditor==true}
							<li><font color="#666666">{t}Don't use an HTML editor by default.{/t}</font></li>
						{/if}
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>{t}Registered since:{/t}</td>
				<td>{$registeredSince|date_format:$opt.format.datelong}</td>
			</tr>

			{if $errorUnknown==true}
				<tr>
					<td colspan="2">
						<span class="errormsg">{t}An unknown error occured.{/t}</span>
					</td>
				</tr>
			{/if}
			<tr><td class="spacer" colspan="2">&nbsp;</td></tr>

			{if $edit==false}
				<tr><td class="spacer" colspan="2">&nbsp;</td></tr>
				<tr>
					<td style="vertical-align:top;">{t}Statistic picture:{/t}</td>
					<td><img src="statpics/{$opt.template.locale}/{$login.userid}.jpg" align=middle></td>
				</tr>
				<tr><td class="spacer" colspan="2"></td></tr>

				<tr>
					<td style="vertical-align:top;">{t}HTML-Code:{/t}</td>
					<td class="help">&lt;img src="{$opt.page.absolute_url|escape|escape}statpics/{$opt.template.locale}/{$login.userid}.jpg" alt="{t 1=$login.username|escape|escape}Opencaching.de-statstic of %1{/t}" title="{t 1=$login.username|escape|escape}Opencaching.de-statstic of %1{/t}" /></td>
				</tr>
				<tr><td class="spacer" colspan="2"></td></tr>

				<tr>
					<td style="vertical-align:top;">{t}BBCode for webforums:{/t}</td>
					<td class="help">[url={$opt.page.absolute_url|escape|escape}viewprofile.php?userid={$login.userid}][img]{$opt.page.absolute_url|escape|escape}statpics/{$opt.template.locale}/{$login.userid}.jpg[/img][/url]</td>
				</tr>
				<tr><td class="spacer" colspan="2"></td></tr>
			{/if}

			<tr>
				<td></td>
				<td></td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td></td>
				<td></td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			{if $edit==true}
				<tr>
					<td class="header-small" colspan="2">
						<input type="submit" name="cancel" value="{t}Cancel{/t}" style="width:120px"/>&nbsp;&nbsp;
						<input type="submit" name="save" value="{t}Submit{/t}" style="width:120px"/>
					</td>
				</tr>
			{/if}
		</table>

{if $edit==true}
	</form>
{/if}