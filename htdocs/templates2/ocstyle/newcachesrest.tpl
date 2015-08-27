{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="32" height="32" alt="" />
	{t}{t 1=$countryName}Latest caches without %1{/t}
</div>

<p class="subtitle-select">
	[<a href="newcaches.php" class="systemlink">{t}All caches{/t}</a>]
	&nbsp;&ndash;&nbsp;
	[<a href="newcaches.php?country={$opt.template.country}" class="systemlink">{t 1=$countryName}Caches in %1{/t}</a>]
	&nbsp;&ndash;&nbsp;
	[<b>{t 1=$countryName}Caches without %1{/t}</b>]
</p>

<table width="100%" class="table">
	{assign var='lastCountry' value=''}

	{foreach name=newCaches from=$newCaches item=newCache}
		{if $newCache.country_name!=$lastCountry}
			<tr><td class="spacer"></td></tr>
			<tr>
				<td colspan="3">
					<table cellspacing="0" cellpadding="0"><tr>
						<td class="content-title-flag"><img src="images/flags/{$newCache.country|lower}.gif" /></td>
						<td><p class="content-title-noshade-size08">{$newCache.country_name|escape}</p></td>
					</tr></table>
				<td>
			</tr>
		{/if}
		<tr><td width="1%">{$newCache.date_created|date_format:$opt.format.date}</td><td class="listicon"><img src="resource2/{$opt.template.style}/images/cacheicon/16x16-{$newCache.type}.gif" width="16" height="16" border="0" /></td><td><a href="viewcache.php?wp={$newCache.wpoc}">{$newCache.cachename|escape}</a> {include file="res_oconly.tpl" oconly=$newCache.oconly} {t}by{/t} <a href="viewprofile.php?userid={$newCache.userid}">{$newCache.username|escape}</a></td></tr>
		{assign var='lastCountry' value=$newCache.country_name}
	{/foreach}
	<tr><td class="spacer"></td></tr>
</table>
