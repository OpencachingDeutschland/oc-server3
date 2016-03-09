{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}My additional profile settings{/t}
</div>

{include file="settingsmenu.tpl"}

<form action="mydetails.php" method="post" style="display:inline;">
	<input type="hidden" name="action" value="change" />
	<table class="table">
		<tr>
			<td colspan="3">
				<span class="boldtext">{t}The following additional information is shown in your <a href="viewprofile.php">public profile</a>:{/t}</span><br /> <!-- TODO: Translation -->
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="" align="middle" />
				<span class="systemlink" style="font-size:10px;">{t}Only the <span class="public-setting">green entries</span> are visible in your <a href="viewprofile.php">public profile</a>.{/t}</span>
			</td>
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

        {include file="displayuseroptions.tpl" dUseroptions=$useroptions1}

		<tr><td class="spacer" colspan="2"></td></tr>

		{if $errorUnknown==true}
			<tr>
				<td colspan="2">
					<span class="errormsg">{t}An unknown error occured.{/t}</span>
				</td>
			</tr>
		{/if}

		<tr>
			<td class="header-small" colspan="3">
				{if $edit==true}
					<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp;
					<input type="submit" name="save" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('save')" />
				{else}
					<input type="submit" name="change" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('change')" />
				{/if}
			</td>
		</tr>
		<tr><td class="spacer">&nbsp;</td></tr>
	</table>
</form>

{if $edit==false}
	<form action="mydetails.php" method="post" style="display:inline;">
		<input type="hidden" name="action" value="changetext" />

		<table class="table">
			<tr>
				<td colspan="3">
					<span class="boldtext systemlink">{t}The following text is displayed in your <a href="viewprofile.php">public profile</a>:{/t}</span>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					{if $desctext==""}<em>({t}no text entered yet{/t})</em>{else}<div class="textblock wide_textblock">{$desctext}<div style="clear:both"></div></div>{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="3"></td></tr>
			<tr>
				<td class="header-small" colspan="3">
					<input type="submit" name="changetext" value="{t}Change{/t}" class="formbutton" onclick="flashbutton('changetext')" />
				</td>
			</tr>
		</table>
	</form>
{/if}



