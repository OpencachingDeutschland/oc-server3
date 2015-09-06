{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" border="0" width="32" height="32" style="margin-right: 10px;" alt="" />
	{t}Choose logo{/t}
</div>

<form name="change" action="change_statpic.php" method="post" enctype="application/x-www-form-urlencoded" style="display: inline;">
	<table class="table">

		<tr>
			<td colspan="2">
				{t}The following settings are stored for your logo:{/t}<br />
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}Text displayed:{/t}</td>
			<td>
				<input type="text" name="statpic_text" maxlength="30" value="{$statpic_text|escape}" class="input200"/>
				{if $statpic_text_error==1}
					<span class="errormsg">
						{t}Text contains invalid charecters!{/t}
					</span>
				{/if}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}Available logos:{/t}</td>
			<td class="help"></td>
			<td style="width:15%"></td>
			<td style="width:15%"></td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		{foreach from=$statpics item=statpicItem}
			<tr>
				<td>{$statpicItem.description|escape}</td>
				<td><input type="radio" name="statpic_style" class="radio" value="{$statpicItem.id}" {if $statpic_style==$statpicItem.id}checked="checked"{/if} /><img src="{$statpicItem.previewpath}" align="middle"></td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
		{foreachelse}
			<tr><td></td><td>No logos available</td></tr>
		{/foreach}

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
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td class="header-small" colspan="2">
				<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp;
				<input type="submit" name="ok" value="{t}Save{/t}" class="formbutton" onclick="submitbutton('ok')" />
			</td>
		</tr>

		<tr>
			<td colspan="3">
				<br />{t}After saving, you may need to press the reload button in your browser to see the selected picture in your profile.{/t}
			</td>
		</tr>
	</table>
</form>
