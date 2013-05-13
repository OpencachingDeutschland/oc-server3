{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<div style="overflow: hidden;">
<div style="float: left; width: 535px">
<ul class="nodot">
	{foreach name=newcaches from=$newcaches item=cacheitem}
		<li class="newcache_list_multi" style="margin-bottom: 8px;">
			{include file="res_cacheicon_22.tpl" cachetype=$cacheitem.type}
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
		</li>
	{/foreach}
</ul>
</div>
<div style="margin-left: 535px; margin-top: 11px; width: 220px; text-align: right">
<a href="map2.php">
<img src="http://maps.googleapis.com/maps/api/staticmap?sensor=false&size=220x220&maptype=roadmap&markers=color:blue|size:small{foreach name=newcaches from=$newcaches item=cacheitem}|{$cacheitem.latitude},{$cacheitem.longitude}{/foreach}"><br />
{t}Large map{/t}
</a>
</div>
</div>
