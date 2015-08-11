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

<table width="100%" class="table">
	<tr>
		<td colspan="3" class="header-small">
			{include file="res_pager.tpl"}
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>

	{foreach name=newCaches from=$newCaches item=newCache}
		<tr>
			<td style="width:1%; vertical-align:center">{$newCache.date_created|date_format:$opt.format.date}</td>
			<td class="listicon"><img src="resource2/{$opt.template.style}/images/cacheicon/16x16-{$newCache.type}.gif" width="16" height="16" border="0" /></td><td style="vertical-align:center"> <a href="viewcache.php?wp={$newCache.wpoc}">{$newCache.cachename|escape}</a> {include file="res_oconly.tpl" oconly=$newCache.oconly} {t}by{/t} <a href="viewprofile.php?userid={$newCache.userid}">{$newCache.username|escape}</a> {if $countryCode == '' && $newCache.country != $defaultcountry}&nbsp;&nbsp;<img src="images/flags/{$newCache.country|lower}.gif" alt="({$newCache.country_name})" title="{$newCache.country_name}" />{/if} </td>
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