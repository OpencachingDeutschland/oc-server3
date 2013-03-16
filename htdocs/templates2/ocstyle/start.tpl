{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

<div class="content-txtbox-noshade">
	<div class="content-txtbox-noshade">
		<p style="line-height: 1.6em;">{$message}</p>
		<div class="buffer" style="width: 500px;">&nbsp;</div>
	</div> 
</div>

{* news or blog *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-news.png" style="align: left; margin-right: 10px;" width="24" height="24" alt="{t}News{/t}" />
		<a href="http://blog.opencaching.de" style="color:rgb(88,144,168); text-decoration: none;">{t}News{/t}</a>
	</p>
</div>
{if !$extern_news}
	<table border="0" cellspacing="0" cellpadding="0">
		{foreach name=news from=$news item=newsitem}
			<tr>
				<td>
					<b>{$newsitem.date|date_format:$opt.format.datetime} ({$newsitem.topic})</b>
					{$newsitem.content}
					{if !$smarty.foreach.news.last}
						<hr />
					{/if}
				</td>
			</tr>
			<tr><td class="spacer"></td></tr>
		{/foreach}
	</table>
{else}
	<div id="blog">
		{$news}
	</div>
{/if}

{* next events *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/cacheicon/event.gif" style="align: left; margin-right: 10px;" width="24" height="24" alt="" />
		{t 1=$usercountry|escape}The next events in %1{/t}
	</p>
</div>
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

{* new logpix *}
<div class="content2-container bg-blue02" style="margin-bottom:6px">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-news.png" style="align: left; margin-right: 10px;" width="24" height="24" alt="{t}News{/t}" />
		<a href="newlogpics.php" style="color:rgb(88,144,168); text-decoration: none;">{t}New log pictures{/t}</a>
	</p>
</div>
{include file="res_logpictures.tpl" logdate=true loguser=true}

{* recommendations *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png" style="align: left; margin-right: 10px;" width="24" height="24" alt="" />
		<a href="tops.php" style="color:rgb(88,144,168); text-decoration: none;">{t}Current top ratings{/t}</a>
	</p>
</div>
<p style="line-height: 1.6em;">{t 1=$usercountry|escape}Geocaches with most ratings in the last 30 days in %1.{/t}</p>
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

{* forum news *}
{if $phpbb_enabled==true}
	<div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>
	<div class="content2-container bg-blue02"> 
		<p class="content-title-noshade-size3"> 
			<img src="resource2/ocstyle/images/misc/32x32-news.png" style="margin-right: 10px;" alt="" width="24" height="24"> 
			<a href="{$phpbb_link|escape}" style="color: rgb(88, 144, 168); text-decoration: none;">{t 1=$phpbb_name|escape}New forum topcis (%1){/t}</a>
		</p> 
	</div>
{* adapted by bohrsty for forums-posts on homepage using RSS-feed
        <div class="content-txtbox-noshade">
                <p style="line-height: 1.6em;">Unser neues Forum findest du unter <a href="http://forum.geocaching-network.org">forum.geocaching-network.org</a>.</p>
                <div class="buffer" style="width: 500px;">&nbsp;</div>
        </div>
*}
<div id="forum">
	{$forum}
</div>
{*
	<ul class="nodot">
		{foreach from=$phpbb_topics item=phpbbItem}
			<li class="newcache_list_multi" style="margin-bottom: 8px;">
				<img src="resource2/ocstyle/images/cacheicon/event-rand1.gif" alt="" style="margin-right: 5px;" width="22" height="22" align="left" border="0"> 
				{$phpbbItem.updated|date_format:$opt.format.datetime}&nbsp;
				<b><a href="{$phpbbItem.link|escape}" target="thegreenhell">{$phpbbItem.title|escape}</a></b>
				von {$phpbbItem.username|escape}
			</li>
		{/foreach}
	</ul>
*}
{/if}

{* new caches *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="align: left; margin-right: 10px;" width="24" height="24" alt="" />
		<a href="newcaches.php" style="color:rgb(88,144,168); text-decoration: none;">{t 1=$usercountry|escape}Newest caches in %1{/t}</a>
	</p>
</div>
<p style="line-height: 1.6em;">({t 1=$count_hiddens 2=$count_founds 3=$count_users}Total of %1 active Caches and %2 founds by %3 users{/t})</p>
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

