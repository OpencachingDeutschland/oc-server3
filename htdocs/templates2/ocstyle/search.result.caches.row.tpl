{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
*
*  include file for search.result.caches.tpl
*  outputs one cache line of the search result 
***************************************************************************}
<!--m-->
<tr>
  <td width="18" class="{$listcolor}">&nbsp;{$position}&nbsp;&nbsp;</td>
  <td width="45" class="{$listcolor}">{if $cache.distance !== null}{$cache.distance|sprintf:"%1.1f"|escape}&nbsp;{/if}</td>
  <td width="32" class="{$listcolor}" rowspan="2"><img src="resource2/{$opt.template.style}/images/cacheicon/{$cache.icon}" title="{$cache.cacheTypeName}" /></td>
  <td width="46" class="{$listcolor}" rowspan="2"><nobr>{include file="res_difficon.tpl" difficulty=$cache.difficulty}{include file="res_terricon.tpl" terrain=$cache.terrain}</nobr></td>
  <td width="448" class="{$listcolor}">{if $cache.isnew}<b class="newsymbol">&nbsp;{t}NEW{/t}&nbsp;</b>&nbsp; {/if}<span style="{include file="res_cachestatus_span.tpl" status=$cache.status}"><a href="viewcache.php?cacheid={$cache.cache_id|escape}"><span style="{if $cache.redname}color: #e00000{/if}">{$cache.name|escape}</span></a></span> &nbsp;{t}by{/t} <a href="viewprofile.php?userid={$cache.user_id|escape}">{$cache.username|escape}</a><!-- Ocprop: <a href="viewcache.php?cacheid={$cache.cache_id|escape}">{$cache.name|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$cache.user_id|escape}">{$cache.username|escape}</a> --></td>
  <td width="74" class="{$listcolor}" rowspan="2" style="padding: 0px">{if $cache.oconly}<img src="resource2/ocstyle/images/misc/is_oconly.png" alt="OConly" title="OConly" style="margin:0px; padding:0px" width="64" height="35" />{/if}</td>
  <td width="110" valign="top" class="{$listcolor}"><nobr>
		{if $cache.firstlog}
			<a href="viewcache.php?cacheid={$cache.cache_id}&log=A#log{$cache.firstlog.id}">{include file="res_logtype.tpl" type=$cache.firstlog.type}</a><a href="viewcache.php?cacheid={$cache.cache_id}&log=A#log{$cache.firstlog.id}">{$cache.firstlog.date|date_format:$opt.format.date}</a>&nbsp;
		{else}
			<img src="resource2/{$opt.template.style}/images/log/16x16-none.png" width="16" height="16" /> --.--.----&nbsp;
		{/if}
	</nobr></td>
</tr>
<!--n-->
<tr>
  <td width="25" class="{$listcolor}">&nbsp;</td>
  <td width="32" class="{$listcolor}" valign="top">{if $cache.direction_deg !== false}<img src="resource2/ocstyle/images/direction/16x16-{$cache.direction_deg}deg.png" title="{t}Cardinal direction:{/t} {$cache.direction_txt}" />&nbsp;{/if}</td>
  <td width="448" class="{$listcolor}" valign="top">
		<p>{strip}
		{if $cache.topratings>0}<img src="images/rating-star.gif" title="{t}Recommendations{/t}" width="17" height="16" />{/if}
		{if $cache.topratings>1}<img src="images/rating-star.gif" title="{t}Recommendations{/t}" width="17" height="16" />{/if}
		{if $cache.topratings>2}<img src="images/rating-plus.gif" title="{t}Recommendations{/t}" width="17" height="16" />{/if}
		{/strip}		
		{foreach from=$cache.desclangs item=desclang}
			<a href="viewcache.php?cacheid={$cache.cache_id}&desclang={$desclang|escape}" style="text-decoration:none"><b><span style="color:blue">{$desclang|escape}</span></b></a>
		{/foreach}
		{$cache.short_desc} &nbsp;</p></td>
  <td width="110" class="{$listcolor}" valign="top">{foreach from=$cache.logs item=log}<a href="viewcache.php?cacheid={$cache.cache_id}&log=A#log{$log.id}">{include file="res_logtype.tpl" type=$log.type}</a>&nbsp;{/foreach}</td>
</tr>
