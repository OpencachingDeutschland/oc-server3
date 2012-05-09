{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE - minimale Änderungen *}
{if $edit==true}
	<form action="mydetails.php" method="post" style="display:inline;">
		<input type="hidden" name="action" value="change" />
{/if}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" border="0" align="middle" width="32" height="32" alt="" />
	<b>{t}My profile details{/t}</b>
</div>

<table class="table">
	<tr>
		<td colspan="3">
			{t}The following detailed information is stored in your userprofile:{/t}<br />
			{if $edit==true}
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="" align="middle" />
				<span style="color:#666666; font-size:10px;">
					{t}Unchecked entries are not visible for other users.<br />
 						 Entries without checkbox are only needed for internal purposes and will never show up in your public profile.{/t}
 				</span>
			{/if}
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
			<td style="vertical-align:top;"><b>{$useropt.name|escape}:</b></td>
			<td style="vertical-align:top;">
				{if $edit==true}
					{if $useropt.internal_use!=1}
						<input type="checkbox" name="chk{$useropt.id}" value="1"{if $useropt.option_visible==1} checked="checked"{/if} class="checkbox" />
					{/if}
				{else}
					{if $useropt.internal_use!=1}
						{if $useropt.option_visible==1}
							<span style="color:#666666;">{t}visible{/t}</span>
						{else}
							<span style="color:#666666;">{t}invisible{/t}</span>
						{/if}
					{else}
						<span style="color:#666666;">{t}internal{/t}</span>
					{/if}
				{/if}
			</td>
			<td>
				{if $edit==true}
					{if $useropt.option_input=="text"}
						<input type="text" name="inp{$useropt.id}" value="{$useropt.option_value|escape}" class="input200" />
					{/if}
					{if $useropt.option_input=="textarea"}
						<textarea class="logs" cols="68" rows="6" name="inp{$useropt.id}">{$useropt.option_value|escape}</textarea>
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
		</tr>
	{foreachelse}
		<tr>
			<td colspan="3">{t}No information on user details found.{/t}</td>
		</tr>
	{/foreach}

	<tr><td class="spacer" colspan="3"></td></tr>

	{if $edit==true}
		<tr>
			<td class="header-small" colspan="3">
				<input type="submit" name="cancel" value="{t}Cancel{/t}" style="width:120px" />&nbsp;&nbsp;
				<input type="submit" name="save" value="{t}Submit{/t}" style="width:120px" />
			</td>
		</tr>
		<tr><td class="spacer" colspan="3"></td></tr>
	{/if}
</table>

{if $edit==true}
	</form>
{/if}
