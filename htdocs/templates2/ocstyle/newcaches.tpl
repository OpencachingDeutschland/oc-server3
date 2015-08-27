{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="32" height="32" alt="" />
	{if $events}{t}Planned events{/t}{elseif $countryCode == ''}{t}Latest caches{/t}{else}{t 1=$countryName|escape}Newest caches in %1{/t}{/if}
</div>

{if !$events}
<p class="subtitle-select">
	[{if $countryCode == ''}<b>{else}<a href="newcaches.php" class="systemlink">{/if}{t}All caches{/t}{if $countryCode == ''}</b>{else}</a>{/if}]
	&nbsp;&ndash;&nbsp;
	[{if $countryCode != ''}<b>{else}<a href="newcaches.php?country={$opt.template.country}" class="systemlink">{/if}{t 1=$countryName}Caches in %1{/t}{if $countryCode != ''}</b>{else}</a>{/if}]
	&nbsp;&ndash;&nbsp;
	[<a href="newcachesrest.php{if $countryCode != ''}?country={$countryCode}{/if}" class="systemlink">{t 1=$countryName}Caches without %1{/t}</a>]
</p>
{/if}

<table width="100%" class="table">
	<tr>
		<td colspan="3" class="header-small" >
			{include file="res_pager.tpl"}
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>

	{foreach name=newCaches from=$newCaches item=newCache}
		<tr>
			<td style="width:1%; vertical-align:center">{$newCache.date_created|date_format:$opt.format.date}</td>
			<td class="listicon">{if $events}<img src="resource2/{$opt.template.style}/images/cacheicon/event-rand{rand min=1 max=4}.gif" alt="{t}Event Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />{else}<img src="resource2/{$opt.template.style}/images/cacheicon/16x16-{$newCache.type}.gif" width="16" height="16" border="0" />{/if}</td><td style="vertical-align:center"> <a href="viewcache.php?wp={$newCache.wpoc}">{$newCache.cachename|escape}</a> {include file="res_oconly.tpl" oconly=$newCache.oconly} {t}by{/t} <a href="viewprofile.php?userid={$newCache.userid}">{$newCache.username|escape}</a> {if $countryCode == '' && $newCache.country != $defaultcountry}&nbsp;&nbsp;<img src="images/flags/{$newCache.country|lower}.gif" alt="({$newCache.country_name})" title="{$newCache.country_name}" />{/if} </td>
		</tr>
	{/foreach}

	<tr><td class="spacer"></td></tr>
	<tr>
		<td colspan="3" class="header-small">
			{include file="res_pager.tpl"}
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>
</table>