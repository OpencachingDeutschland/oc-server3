{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{strip}
<form method="POST" action="adminreports.php">
	<input type="hidden" name="rid" value="{$id}" />
	<input type="hidden" name="cacheid" value="{$cacheid}" />
	<input type="hidden" name="ownerid" value="{$ownerid}" />

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
		{t}Reported caches{/t}
	</div>

	<div class="content2-container">
	{if $error == 1}
		<p style="line-height: 1.6em;">{t}The report is already assigned to another admin!{/t}</p>
	{elseif $error == 2}
		<p style="line-height: 1.6em;">{t}The report is already assigned to you!{/t}</p>
	{elseif $error == 3}
		<p style="line-height: 1.6em;">{t}You can not work on this report! Another admin is already pursuing it.{/t}</p>
	{elseif $error == 4}
		<p style="line-height: 1.6em;">{t}To work on a report you have to assign it to you!{/t}</p>
	{/if}

	{if $list == true}
		<table class="narrowtable">
		<tr><th>{t}ID{/t}</th><th>{t}Name{/t}</th><th>{t}Owner{/t}</th><th>{t}Reporter{/t}</th><th>{t}Date{/t}</th></tr>
		{assign var="otheradmins" value=0}
		{foreach from=$reportedcaches item=rc}
			<tr>
			{if $rc.otheradmin > $otheradmins}
				<td colspan="5"><p style="line-height: 2.5em;">{t}(*) New reports{/t}</p>
				</td></tr>
				<tr><th>{t}ID{/t}</th><th>{t}Name{/t}</th><th>{t}Owner{/t}</th><th>{t}Reporter{/t}</th><th>{t}Admin{/t}</th><th>{t}Date{/t}</th></tr>
				{assign var="otheradmins" value=$rc.otheradmin}
			{/if}
			<td><a href="adminreports.php?id={$rc.id}">{$rc.id}</td>
			<td><a href="adminreports.php?id={$rc.id}">{$rc.new|escape}{$rc.name|escape}</a></td>
			<td>{$rc.ownernick|escape}</td>
			<td>{$rc.username|escape}</td>
			{if $otheradmins}
				<td>{$rc.adminname|escape}</td>
			{/if}
			<td style="white-space: nowrap;">{$rc.lastmodified|date_format:$opt.format.date}</td></tr>
		{foreachelse}
			<tr><td colspan=5>{t}No reported caches{/t}</td></tr>
		{/foreach}
		</table>
		{if $reportedcaches != NULL and $otheradmins==0}
			<p style="line-height: 2.5m;">{t}(*) New reports{/t}</p>
		{/if}
	{else}
		<p style="line-height: 1.6em;">{t}Details for report of {/t} <a href="viewcache.php?cacheid={$cacheid}" target="_blank">{$cachename|escape}</a> {t} by {/t} <a href="viewprofile.php?userid={$userid}" target="_blank">{$usernick|escape}</a>
		&nbsp; &nbsp; &nbsp;
		[<a href="http://www.geocaching.com/seek/nearest.aspx?t=k&origin_lat={$cache.latitude}&amp;origin_long={$cache.longitude}&amp;dist=1&amp;submit3=Search" target="_blank">{t}Nearby search at geocaching.com{/t}</a>]
		&nbsp; &nbsp; &nbsp;
		{foreach from=$cachexternal key=extname item=exturl}
			[<a href="{$exturl|replace:_cache_id_:$cacheid}" target="_blank">{$extname}</a>] &nbsp;
		{/foreach}
		{$external_maintainer_msg}
		</p>
		{if $created != null}
			<p style="line-height: 1.6em;"><b>{t}Created at:{/t}</b>&nbsp;{$created|date_format:$opt.format.datelong}</p>
		{/if}
		{if $lastmodified != $created}
			<p style="line-height: 1.6em;"><b>{t}Last modified{/t}{t}#colonspace#{/t}</b>&nbsp;{$lastmodified|date_format:$opt.format.datelong}</p>
		{/if}
		<p style="line-height: 1.6em;"><b>{t}State:{/t}</b>&nbsp;{$status}&nbsp;&nbsp;<b>Admin:</b>&nbsp;{if $adminnick==''}{t}not assigned{/t}{else}{if $otheradmin}<font color="red"><b>{/if}{$adminnick|escape}{if $otheradmin}</b></font>{/if}{/if}</p>
		<p style="line-height: 1.6em;"><b>{t}Reason:{/t}</b>&nbsp;{$reason|escape|nl2br}</p>
		<p style="line-height: 1.6em; margin-bottom:16px"><b>{t}Comment:{/t}</b>&nbsp;{$note|escape|nl2br}</p>

		<div class="content2-container bg-blue02">
  		<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/description/22x22-misc.png" style="margin-right: 10px;" width="22" height="22" alt="" /> 
	  		{t}Action{/t}
	  	</p>
	  </div>

		<p style="line-height: 1.6em; margin-bottom:24px">
		{if !$ownreport}
			<input type="submit" name="assign" value="{t}Assign to me{/t}" class="formbutton" onclick="submitbutton('assign')" />
		{else}
			&nbsp;<input type="submit" name="contact" value="{t}Contact owner{/t}" class="formbutton" onclick="submitbutton('contact')" />&nbsp;<input type="submit" name="contact_reporter" value="{t}Contact reporter{/t}" class="formbutton" onclick="submitbutton('contact_reporter')" />&nbsp;&nbsp;<input type="submit" name="done" value="{t}Mark as finished{/t}" class="formbutton" onclick="submitbutton('done')" />
			</p>

			<div class="content2-container bg-blue02">
				<p class="content-title-noshade-size2">
					<img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" style="margin-right: 10px;" width="22" height="22" alt="" /> 
					{t}Set state{/t}
				</p>
			</div>

			<p style="line-height: 1.6em;">
				<a href="log.php?cacheid={$cacheid}&logtype=10&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-active.png" />{t}Ready for search{/t}</a>
				&nbsp; &nbsp;
				<a href="log.php?cacheid={$cacheid}&logtype=11&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-disabled.png" />{t}Temporary not available{/t}</a>
				&nbsp; &nbsp;
				<a href="log.php?cacheid={$cacheid}&logtype=9&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-archived.png" />{t}Archived{/t}</a>
				&nbsp; &nbsp;
				<a href="log.php?cacheid={$cacheid}&logtype=13&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-locked.png" />{t}Locked, visible{/t}</a>
				&nbsp; &nbsp;
				<a href="log.php?cacheid={$cacheid}&logtype=14&teamcomment=1" target="_blank"><img src="resource2/{$opt.template.style}/images/log/16x16-locked-invisible.png" />{t}Locked, invisible{/t}</a>
			</p>
			{if $otheradmin}
				</p><br />{t}Warning: This report is already assigned to another admin. Consult him first before you assume the report!{/t}
			{/if}
		{/if}
		<br />

		{include file=adminhistory.tpl reportdisplay=true showhistory=true}
	{/if}
	</div>

</form>
{/strip}
