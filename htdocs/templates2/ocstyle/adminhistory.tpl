{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="World" />
	{t}Cache history{/t}
</div>

<form method="POST" action="adminhistory.php">
	<p>
		<b>{t}Cache code{/t}:</b> &nbsp;
		<input type="text" width="10" name="wp" /> &nbsp;
		<input type="submit" name="submitform" value="{t}Show{/t}" class="formbutton" onclick="submitbutton('submitform')" />
	</p>
</form>

{if $showhistory}
	<p>
		<a href="viewcache.php?cacheid={$cache.cache_id}">{$cache.name}</a>
		{t}by{/t}
		<a href="viewprofile.php?userid={$cache.user_id}">{$ownername}</a>
	</p>
	<br />

	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Cache reports{/t}
		</p>
	</div>
	{include file="adminreport_history.tpl"}
	<br />

	<div class="content2-container bg-blue02">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
			{t}Deleted logs{/t}
		</p>
	</div>
	<div class="content2-container">
		{include file="res_logentry.tpl" header=false footer=false footbacklink=false cache=$cache logs=$deleted_logs}
	</div>
	<br />

	{include file="res_status_changes.tpl"}

{else}
	<p class="errormsg">{$error}</p>
{/if}
