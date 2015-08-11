{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

<div class="content-txtbox-noshade">
	<div class="content-txtbox-noshade">
		<p class="startmessage">{$message}</p>
		<div class="buffer" style="width: 500px;">&nbsp;</div>
	</div> 
</div>

{foreach from=$sections item=section}

{* news or blog *}
{if $section == 'news'}
<div>
	<div class="content2-container bg-blue02">
		<table class="none" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td>
					<p class="content-title-noshade-size3">
						<img src="resource2/{$opt.template.style}/images/misc/32x32-news.png" style="margin-right: 10px;" width="24" height="24" alt="" />
						<a href="http://blog.opencaching.de/" style="color:rgb(88,144,168); text-decoration: none;">{t}News{/t}</a>
						&nbsp; <span class="content-title-link">[<a href="http://blog.opencaching.de/">{t}more{/t}...</a>]</span>
					</p>
				</td>
				{if "$newsfeed" != ""}
					<td style="text-align:right">
						<a href="{$newsfeed}"><img src="resource2/ocstyle/images/media/22x22-feed.png" /></a>
					</td>
					<td width="4px"></td>
				{/if}
			</tr>
		</table>
	</div>
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
{if $news|@count}
		{include file="res_rssparser.tpl" rss=$news}
{else}
		<p><em>{t}currently not available{/t}</em></p>
{/if}
	</div>
	<div class="buffer" style="width: 500px;">&nbsp;</div>
{/if}

{* next events *}
{elseif $section == 'events'}
<div class="content2-container bg-blue02 content2-section-no-p">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/cacheicon/event.gif" style="margin-right: 10px;" width="24" height="24" alt="" />
		{t 1=$usercountry|escape}The next events in %1{/t}
		{if $total_events > $events|@count}
			&nbsp; <span class="content-title-link">[<a href="newcaches.php?cachetype=6">{t}more{/t}...</a>]</span>
		{/if}
	</p>
</div>
<div class="content2-section-no-p">
	{include file="res_newevents.tpl" events=$events}
</div>

{* new logpix *}
{elseif $section == 'logpics'}
<div class="content2-container bg-blue02" style="margin-bottom:6px">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-pictures.gif" style="margin-right: 10px;" width="24" height="24" />
		<a href="newlogpics.php" style="color:rgb(88,144,168); text-decoration: none;">{t}New log pictures{/t}</a>
	&nbsp; <span class="content-title-link">[<a href="newlogpics.php">{t}more{/t}...</a>]</span>
	</p>
</div>
<div style="height:2px"></div>
{include file="res_logpictures.tpl" logdate=true loguser=true}

{* recommendations *}
{elseif $section == 'recommendations'}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png" style="margin-right: 10px;" width="24" height="24" alt="" />
		<a href="tops.php" style="color:rgb(88,144,168); text-decoration: none;">{t}Current top ratings{/t}</a>
	</p>
</div>
<p style="line-height: 1.6em;">{t 1=$usercountry|escape 2=$toprating_days}Geocaches with most ratings in the last %2 days in %1.{/t}</p>

<div style="margin-bottom:16px">
	{include file="res_newratings.tpl" topratings=$topratings}
</div>

{* forum news *}
{elseif $section == 'forum'}
{if $phpbb_enabled==true}
	<div class="buffer" style="width: 500px;height: 2px;">&nbsp;</div>
	<div class="content2-container bg-blue02"> 
		<p class="content-title-noshade-size3"> 
			<img src="resource2/ocstyle/images/misc/32x32-news.png" style="margin-right: 10px;" alt="" width="24" height="24" /> 
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
{if $forum|@count}
	{include file="res_rssparser.tpl" rss=$forum}
{else}
	<p><em>{t}currently not available{/t}</em></p>
{/if}
</div>
<div class="buffer" style="width: 500px;">&nbsp;</div>
{*
	<ul class="nodot">
		{foreach from=$phpbb_topics item=phpbbItem}
			<li class="newcache_list_multi" style="margin-bottom: 8px;">
				<img src="resource2/ocstyle/images/cacheicon/event-rand1.gif" alt="" style="margin-right: 5px;" width="22" height="22" align="left" border="0" /> 
				{$phpbbItem.updated|date_format:$opt.format.datetime}&nbsp;
				<b><a href="{$phpbbItem.link|escape}" target="thegreenhell">{$phpbbItem.title|escape}</a></b>
				von {$phpbbItem.username|escape}
			</li>
		{/foreach}
	</ul>
*}
{/if}

{* new caches *}
{elseif $section == 'newcaches'}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="24" height="24" alt="" />
		<a href="newcaches.php" style="color:rgb(88,144,168); text-decoration: none;">{t 1=$usercountry|escape}Newest caches in %1{/t}</a>
	&nbsp; <span class="content-title-link">[<a href="newcaches.php?country={$usercountryCode}">{t}more{/t}...</a>]</span>
	</p>
</div>
<p style="line-height: 1.6em;">({t 1=$count_hiddens 2=$count_founds 3=$count_users}Total of %1 active Caches and %2 founds by %3 users{/t})</p>
<div class="content2-section-no-p">
	{include file="res_newcaches.tpl" newcaches=$newcaches}
</div>

{/if}
{/foreach}

