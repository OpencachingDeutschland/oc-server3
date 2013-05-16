{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{if $minimap_enabled}
<div>
<img class="img-minimap" style="margin-top: 16px; margin-right: 20px; width: 220px; height: 220px; float: right;" src="{$minimap_url|escape}{foreach name=newcaches from=$newcaches item=cacheitem}|{$cacheitem.latitude},{$cacheitem.longitude}{/foreach}">
{/if}
<ul class="nodot">
	{foreach name=newcaches from=$newcaches item=cacheitem}
		<li class="newcache_list_multi" style="margin-bottom: 8px;">
			{include file="res_cacheicon_22.tpl" cachetype=$cacheitem.type}
			<div style="margin-left: 29px;">
			{$cacheitem.date_created|date_format:$opt.format.date}&nbsp;
			<b><a class="links" href="viewcache.php?cacheid={$cacheitem.cache_id}">{$cacheitem.name|escape}</a></b>
			{t}by{/t}
			<b><a class="links" href="viewprofile.php?userid={$cacheitem.user_id}">{$cacheitem.username|escape}</a></b><br />
			<strong>
				<p class="content-title-noshade">
					{$cacheitem.adm1|escape} {if $cacheitem.adm1!=null & $cacheitem.adm2!=null} &gt; {/if}
					{$cacheitem.adm2|escape} {if ($cacheitem.adm2!=null & $cacheitem.adm4!=null) | ($cacheitem.adm1!=null & $cacheitem.adm4!=null)} &gt; {/if}
					{$cacheitem.adm4|escape}
				</p>
			</strong>
			</div>
		</li>
	{/foreach}
</ul>
{if $minimap_enabled}
</div>
{/if}
