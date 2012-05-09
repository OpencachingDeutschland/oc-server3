{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Latest caches without germany{/t}" />
	{t}Latest caches without germany{/t}
</div>

<table width="100%" class="table">
	{assign var='lastCountry' value=''}

	{foreach name=newCaches from=$newCaches item=newCache}
		{if $newCache.country_name!=$lastCountry}
			<tr><td class="spacer"></td></tr>
			<tr><td><p class="content-title-noshade-size1"><b>{$newCache.country_name|escape}</b></p></td><tr>
		{/if}
		<tr><td>{$newCache.date_created|date_format:$opt.format.date} - <img src="resource2/{$opt.template.style}/images/cacheicon/{$newCache.icon_large}" width="16" height="16" border="0" alt="Cache" title="Cache" style="margin-top:4px;" /> <a href="viewcache.php?wp={$newCache.wpoc}">{$newCache.cachename|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$newCache.userid}">{$newCache.username|escape}</a></td></tr>
		{assign var='lastCountry' value=$newCache.country_name}
	{/foreach}
</table>