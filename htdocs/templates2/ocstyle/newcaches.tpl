{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Latest caches{/t}" />
	{t}Latest caches{/t}
</div>

<table width="100%" class="table">
	<tr>
		<td colspan="3" class="header-small">
			{include file="res_browse_left.tpl" page="newcaches"}

			{section name=page start=$firstpage loop=$lastpage+1 step=100}
				{if $smarty.section.page.index!=$startat}
					<a href="newcaches.php?startat={$smarty.section.page.index}">{$smarty.section.page.index/$perpage+1}</a>
				{else}
					<b>{$smarty.section.page.index/$perpage+1}</b>
				{/if}
			{/section}

			{include file="res_browse_right.tpl" page="newcaches"}
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>

	{foreach name=newCaches from=$newCaches item=newCache}
		<tr>
			<td style="width:1%; vertical-align:center">{$newCache.date_created|date_format:$opt.format.date}</td>
			<td class="listicon"><img src="resource2/{$opt.template.style}/images/cacheicon/16x16-{$newCache.type}.gif" width="16" height="16" border="0" /></td><td style="vertical-align:center"> <a href="viewcache.php?wp={$newCache.wpoc}">{$newCache.cachename|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$newCache.userid}">{$newCache.username|escape}</a> {if $newCache.country != $defaultcountry}&nbsp;&nbsp;<img src="images/flags/{$newCache.country|lower}.gif" >{/if} </td>
		</tr>
	{/foreach}

	<tr><td class="spacer"></td></tr>
	<tr>
		<td colspan="3" class="header-small">
			{include file="res_browse_left.tpl" page="newcaches"}

			{section name=page start=$firstpage loop=$lastpage+1 step=100}
				{if $smarty.section.page.index!=$startat}
					<a href="newcaches.php?startat={$smarty.section.page.index}">{$smarty.section.page.index/$perpage+1}</a>
				{else}
					<b>{$smarty.section.page.index/$perpage+1}</b>
				{/if}
			{/section}

			{include file="res_browse_right.tpl" page="newcaches"}
		</td>
	</tr>
	<tr><td class="spacer"></td></tr>
</table>