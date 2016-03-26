{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}My profile details{/t}
</div>

<form action="mydetails.php" method="post" style="display:inline;">
	<input type="hidden" name="action" value="change" />

<table class="table">
	<tr>
		<td colspan="3">
			<span class="boldtext">{t}The following additional information is stored in your userprofile:{/t}</span>
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

	{foreach from=$useroptions item=useropt}
		<tr>
			<td style="vertical-align:top; width:10px"><nobr>{$useropt.name|escape}{t}#colonspace#{/t}:</nobr></td>
			<td>
				{if $edit==true}
					{if $useropt.option_input=="text"}
						<input type="text" name="inp{$useropt.id}" value="{$useropt.option_value|escape}" class="input200" />
					{/if}
					{if $useropt.option_input=="textarea"}
						<textarea class="logs" cols="68" rows="6" name="inp{$useropt.id}" style="max-width:500px; max-height:250px">{$useropt.option_value|escape}</textarea>
					{/if}
					{if $useropt.option_input=="checkbox"}
						<input type="checkbox" class="checkbox" name="inp{$useropt.id}" value="1" {if $useropt.option_value=="1"}checked="checked"{/if} />
					{/if}		
				{else}
					{if $useropt.option_input=="checkbox"}
						{if $useropt.option_value=="1"}
							{t}Yes{/t}
						{else}
							{t}No{/t}
						{/if}
					{else}
						{$useropt.option_value|escape}
					{/if}
				{/if}
			</td>
			<td style="vertical-align:top;">
				{if $edit==true}
					{if $useropt.internal_use!=1}
						<input type="checkbox" name="chk{$useropt.id}" value="1"{if $useropt.option_visible==1} checked="checked"{/if} class="checkbox" /> {t}show{/t}
					{/if}
				{else}
					{if $useropt.internal_use!=1}
						{if $useropt.option_visible==1}
							<span style="color:#666666;">{t}visible{/t}</span>
						{else}
							<span style="color:#666666;">{t}invisible{/t}</span>
						{/if}
					{else}
						<!-- <span style="color:#666666;">{t}internal{/t}</span> -->
					{/if}
				{/if}
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="3">{t}No information on user details found.{/t}</td>
		</tr>
	{/foreach}

	<tr><td class="spacer" colspan="3"></td></tr>

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
