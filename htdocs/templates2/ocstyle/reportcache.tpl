{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{capture name="cachelink"}
	<a href="viewcache.php?cacheid={$cacheid}">{$cachename|escape}</a>
{/capture}

{if $success==true}
	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/profile/22x22-email.png" style="margin-right: 10px;" width="22" height="22" alt="Report submitted" />
		{t 1=$smarty.capture.cachelink}Report for %1 submitted{/t}
	</div>

	<table class="table">
		<tr>
			<td colspan="2">
				{t}Reason:{/t} {$reasontext|escape}
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>

		<tr><td colspan="2">{t}Comment:{/t}</td></tr>
		<tr>
			<td colspan="2">
				{$note|escape|nl2br}
			</td>
		</tr>
	</table>
{else}
	<form action="reportcache.php" method="post">
		<input type="hidden" name="cacheid" value="{$cacheid}"/>

	  <div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/profile/22x22-email.png" style="margin-right: 10px;" width="22" height="22" alt="" />
			{t 1=$smarty.capture.cachelink}Report %1{/t}
		</div>

		<table class="table">
			<tr>
				<td colspan="2" class="info">
					{t}Prior to reporting a cache to your Opencaching team you should try to
					contact the owner, to solve possible problems immediate user to user.
					This does not apply for caches violating the Opencaching terms of use
					in a way, that requires immediate action of an Opencaching administrator.{/t}
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">{t}Reason:{/t} 
					<select name="reason">
						<option value="0" {if $reason==0}selected="selected"{/if}>{t}=== Please select ==={/t}</option>
						{foreach from=$reasons item=reasonItem}
							<option value="{$reasonItem.id}" {if $reason==$reasonItem.id}selected="selected"{/if}>{$reasonItem.name|escape}</option>
						{foreachelse}
							<option value="1" class="input400">{t}Default{/t}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			{if $errorReasonEmpty==true}
				<tr><td colspan="2" class="errormsg">{t}You have to select a reason for your report!{/t}</td></tr>
			{/if}

			<tr>
				<td colspan="2">{t}Comment: (required){/t}</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea class="logs" name="note" cols="68" rows="15">{$note|escape}</textarea>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			{if $errorNoteEmpty==true}
				<tr><td colspan="2" class="errormsg">{t}It is required to enter a comment for reporting a cache!{/t}</td></tr>
			{/if}

			<tr><td class="spacer" colspan="2"></td></tr>

			{if $errorUnkown==true}
				<tr><td colspan="2" class="errormsg">{t}An unknown error occured. Reporting failed.{/t}</td></tr>
			{/if}

			<tr>
				<td class="header-small" colspan="2">
					<!-- <input type="reset" name="cancel" value="{t}Reset{/t}" class="formbutton" onclick="flashbutton('cancel')" >&nbsp;&nbsp; -->
					<input type="submit" name="ok" value="{t}Send{/t}" class="formbutton" onclick="submitbutton('ok')" />
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
		</table>
	</form>
{/if}