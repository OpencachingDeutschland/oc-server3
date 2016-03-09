{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}Personal data{/t}
</div>

 {include file="settingsmenu.tpl"}

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
				<td class="public-setting" style="width: 130px">{t}Username:{/t}</td>
				<td class="public-setting">
					{if $edit==true}
						<input type="text" name="username" class="public-setting" value="{$username|escape}" maxlength="60" size="30" />
						{if $usernameErrorInvalidChars==true}
							<span class="errormsg">{t}The username contains invalid characters.{/t}</span>
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

			{include file="displayuseroptions.tpl" dUseroptions=$useroptions5}

			<tr>
				<td class="public-setting">{t}Registered since:{/t}</td>
				<td class="public-setting">{$registeredSince|date_format:$opt.format.datelong}</td>
			</tr>

            {if $error==true || $errorlen==true}
                <tr>
                    <td class="errormsg" colspan="3">
                        {t}Error while saving.{/t}<br />
                        {if $error==true}{t}Illegal characters found in{/t}{$errormsg|escape}<br />{/if}
                        {if $errorlen==true}{t}Field values too long in{/t}{$errormsglen|escape}<br />{/if}
                        {t}Original values were restored.{/t}
                    </td>
                </tr>
            {/if}

            <tr><td class="spacer" colspan="3"></td></tr>

			{if $errorUnknown==true}
				<tr>
					<td colspan="3">
						<span class="errormsg">{t}An unknown error occured.{/t}</span>
					</td>
				</tr>
			{/if}

			<tr><td class="spacer" colspan="2"></td></tr>
			<tr>
				<td class="header-small" colspan="3">
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

