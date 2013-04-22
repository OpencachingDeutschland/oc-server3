{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<ul class="nodot">
	{foreach name=events from=$events item=eventitem}
		<li class="newcache_list_multi" style="margin-bottom: 8px;">
			<img src="resource2/{$opt.template.style}/images/cacheicon/event-rand{rand min=1 max=4}.gif" alt="{t}Event Geocache{/t}" border="0" width="22" height="22" align="left" style="margin-right: 5px;" />
			{$eventitem.date_hidden|date_format:$opt.format.date}&nbsp;
			<b><a class="links" href="viewcache.php?cacheid={$eventitem.cache_id}">{$eventitem.name|escape}</a></b> 
			{t}by{/t} 
			<b><a class="links" href="viewprofile.php?userid={$eventitem.user_id}">{$eventitem.username|escape}</a></b><br />
			<strong>
				<p class="content-title-noshade">
					{$eventitem.adm1|escape} {if $eventitem.adm1!=null & $eventitem.adm2!=null} &gt; {/if}
					{$eventitem.adm2|escape} {if ($eventitem.adm2!=null & $eventitem.adm4!=null) | ($eventitem.adm1!=null & $eventitem.adm4!=null)} &gt; {/if}
					{$eventitem.adm4|escape}
				</p>
			</strong>
		</li>
	{/foreach}	
</ul>