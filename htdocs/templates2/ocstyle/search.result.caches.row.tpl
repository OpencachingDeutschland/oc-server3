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
    <td class="{$listcolor}">&nbsp;</td>
      <td class="{$listcolor}">{if $cache.distance !== null}{$cache.distance|sprintf:"%1.1f"|escape}&nbsp;{/if}</td>
      <td class="{$listcolor}" rowspan="2"><img src="resource2/{$opt.template.style}/images/cacheicon/{$cache.icon}" title="{$cache.cacheTypeName}" /></td>
      <td class="{$listcolor}" rowspan="2"><nobr>{include file="res_difficon.tpl" difficulty=$cache.difficulty}</nobr></td>
    <td class="{$listcolor}" rowspan="2"><nobr>{include file="res_terricon.tpl" terrain=$cache.terrain}</nobr></td>
    <td class="{$listcolor}" colspan="2">{if $cache.isnew}<b class="newsymbol">&nbsp;{t}NEW{/t}&nbsp;</b>&nbsp; {/if}<span style="{include file="res_cachestatus_span.tpl" status=$cache.status}"><a href="viewcache.php?cacheid={$cache.cache_id|escape}"><span style="{if $cache.redname}color: #e00000{/if}">{$cache.name|escape}</span></a></span> &nbsp;{t}by{/t} <a href="viewprofile.php?userid={$cache.user_id|escape}">{$cache.username|escape}</a><!-- Ocprop: <a href="viewcache.php?cacheid={$cache.cache_id|escape}">{$cache.name|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$cache.user_id|escape}">{$cache.username|escape}</a> --></td>
    {if $creationdate}
        <td class="{$listcolor}">{$cache.date_created|date_format:$opt.format.date}</td>
    {else}
        <td class="{$listcolor}" rowspan="2" style="padding: 0px">
            &nbsp;{if $cache.oconly}<img src="resource2/ocstyle/images/misc/is_oconly.png" alt="OConly" title="OConly" style="margin:0px; padding:0px" width="64" height="35" />{/if}
        </td>
    {/if}
    <td class="{$listcolor}" valign="top"><nobr>
        {if $cache.firstlog}
            <nobr><a href="viewcache.php?cacheid={$cache.cache_id}&log=A#log{$cache.firstlog.id}">{include file="res_logtype.tpl" type=$cache.firstlog.type}</a>&nbsp;<a href="viewcache.php?cacheid={$cache.cache_id}&log=A#log{$cache.firstlog.id}">{$cache.firstlog.date|date_format:$opt.format.date}</a>&nbsp;&nbsp;&nbsp;</nobr>
        {else}
            <img src="resource2/{$opt.template.style}/images/log/16x16-none.png" width="16" height="16" />&nbsp;--.--.----&nbsp;
        {/if}
    </nobr>
      </td>
</tr>
<!--n-->
<tr>
    <td class="{$listcolor}">&nbsp;</td>
    <td class="{$listcolor}" valign="top">{if $cache.direction_deg !== false}<img src="resource2/ocstyle/images/direction/16x16-{$cache.direction_deg}deg.png" title="{t}Cardinal direction:{/t} {$cache.direction_txt}" />&nbsp;{/if}</td>
    <td class="{$listcolor}" colspan="2" valign="top">
    <p class="truncate">
        {strip}
            {if $cache.topratings<4}
                {if $cache.topratings>0}<img src="images/rating-star.gif" title="{t}Recommendations{/t}" width="14" height="13" style="margin-top: -3px;"/>{/if}
                {if $cache.topratings>1}<img src="images/rating-star.gif" title="{t}Recommendations{/t}" width="14" height="13" style="margin-top: -3px;"/>{/if}
                {if $cache.topratings>2}<img src="images/rating-star.gif" title="{t}Recommendations{/t}" width="14" height="13" style="margin-top: -3px;"/>{/if}
            {/if}
            {if $cache.topratings>3}
                <b><span class="txtblack" style="color:#02c602; font-size: 13px;">{$cache.topratings}x</span></b>
                <img src="images/rating-star.gif" title="{t}Recommendations{/t}" width="14" height="13" style="margin-top: -3px;"/>
            {/if}
        {/strip}
        {foreach from=$cache.desclangs item=desclang}
            <a href="viewcache.php?cacheid={$cache.cache_id}&desclang={$desclang|escape}" style="text-decoration:none"><b><span style="color:blue">{$desclang|escape}</span></b></a>
        {/foreach}
        {$cache.short_desc|escape} &nbsp;</p>
    </td>
    {if $creationdate}
        <td  class="{$listcolor}" valign="top">{if $cache.oconly}<img src="resource2/ocstyle/images/misc/15x15-oc.png"/>{/if}&nbsp;</td>
    {/if}
      <td  class="{$listcolor}" valign="top"><nobr>{foreach from=$cache.logs item=log}<a href="viewcache.php?cacheid={$cache.cache_id}&log=A#log{$log.id}">{include file="res_logtype.tpl" type=$log.type}</a>&nbsp;{/foreach}&nbsp;&nbsp;</nobr></td>
</tr>
