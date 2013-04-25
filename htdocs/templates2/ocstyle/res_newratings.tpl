{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<ul class="nodot">			
	{foreach name=topratings from=$topratings item=cacheitem}
		<li class="newcache_list_multi" style="margin-bottom: 8px;">
			{include file="res_cacheicon_22.tpl" cachetype=$cacheitem.type}
			{if $cacheitem.cRatings>=1}<img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}">{/if}
			{if $cacheitem.cRatings>=2}<img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}">{/if}
			{if $cacheitem.cRatings==3}<img src="images/rating-star.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}">{/if}
			{if $cacheitem.cRatings>3}<img src="images/rating-plus.gif" border="0" alt="{t 1=$cacheitem.cRatings}%1 Recommendations in the last 30 days{/t}">{/if}
			&nbsp;
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
<p style="line-height: 1.6em;">{t}You can find more recommendations &gt;<a href="tops.php">here</a>&lt;.{/t}</p>