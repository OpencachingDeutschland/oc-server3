{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" border="0" width="32px" height="32px" style="align: left; margin-right: 10px;" alt="Mein Profil" />
	{t 1=$login.username}Hello %1{/t}
</div>

<div class="content2-container bg-blue02" style="margin-top:20px;">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" width="22" height="22"  style="align: left; margin-right: 10px;" alt="{t}Logs{/t}"" />&nbsp;
		{t 1=$found}Geocaches found: %1{/t}
	</p>
</div>

<p style="line-height: 1.6em;">[<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=1,7">{t}Show all{/t}</a>] - <b>{t}Your latest log entries:{/t}</b></p>

<table class="table">
	{foreach from=$logs item=logItem}
		<tr>
			<td>{include file="res_logtype.tpl" type=$logItem.type}</td>
			<td>{$logItem.date|date_format:$opt.format.datelong}</td>
			<td><a href="viewcache.php?wp={$logItem.wp_oc}">{$logItem.name|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$logItem.userid}">{$logItem.username|escape}</a></td>
		</tr>
	{foreachelse}
		<tr><td>{t}No entries found{/t}</td></tr>
	{/foreach}
</table>

<div class="content2-container bg-blue02" style="margin-top:20px;">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/22x22-traditional.gif" width="22" height="22"  style="align: left; margin-right: 10px;" alt="{t}Caches{/t}" />&nbsp;
		{t 1=$hidden}Geocaches hidden: %1{/t}
	</p>
</div>

<p style="line-height: 1.6em;">
	[<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=0"">{t}Show all{/t}</a>] - 
	<b>{t}Your latest Geocaches hidden:{/t}</b>
</p>

<table class="table">
	{foreach from=$caches item=cacheItem}
		<tr>
			<td>{include file="res_cachestatus.tpl" status=$cacheItem.status}</td>
			<td nowrap="nowrap">{$cacheItem.date_hidden|date_format:$opt.format.datelong}</td>
			<td><a href="viewcache.php?wp={$cacheItem.wp_oc}">{$cacheItem.name|escape}</a></td>
		</tr>
	{foreachelse}
		<tr><td>{t}No Geocaches hidden{/t}</td></tr>
	{/foreach}
</table>

<p style="line-height: 1.6em;"><b>{t}Not published Geocaches will be available{/t}</b></p>

<table class="table">
	{foreach from=$notpublished item=notpublishedItem}
		<tr>
			<td>{include file="res_cachestatus.tpl" status=$notpublishedItem.status}</td>
			<td>{$notpublishedItem.date_activate|date_format:$opt.format.datelong}</td>
			<td><a href="viewcache.php?wp={$notpublishedItem.wp_oc}">{$notpublishedItem.name|escape}</a></td>
		</tr>
	{foreachelse}
		<tr><td>{t}All Geocaches are published{/t}</td></tr>
	{/foreach}
</table>

<p class="content-title-noshade-size3">
	<img src="resource2/{$opt.template.style}/images/misc/22x22-email.png" width="22" height="22"  style="align: left; margin-right: 10px;" alt="{t}E-Mails sent{/t}" />&nbsp;
	{t 1=$emails}E-Mails sent: %1{/t}
</p>

<div class="buffer">&nbsp;</div>
