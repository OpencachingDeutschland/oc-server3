{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}

<div class="content-txtbox-noshade">
	<div class="content-txtbox-noshade">
		<p style="line-height: 1.5em;">{$message}</p>
		<div class="buffer" style="width: 500px;">&nbsp;</div>
	</div> 
</div>

{* news or blog *}
<div>
	<div class="content2-container bg-blue02">
		<table class="none" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td>
					<p class="content-title-noshade-size3">
						<img src="resource2/{$opt.template.style}/images/misc/32x32-news.png" style="align: left; margin-right: 10px;" width="24" height="24" alt="{t}News{/t}" />
						<a href="http://blog.opencaching.de/" style="color:rgb(88,144,168); text-decoration: none;">{t}News{/t}</a>
						&nbsp; <span style="color:black; font-size:0.8em; font-weight:normal">[<a href="http://blog.opencaching.de/">{t}more{/t}...</a>]</span>
					</p>
				</td>
				{if "$newsfeed" != ""}
					<td style="text-align:right">
						<a href="{$newsfeed}"><img src="resource2/ocstyle/images/misc/22x22-feed-icon.png"></a>
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
{if !$rsserror}
		{include file="res_rssparser.tpl" rss=$news}
{else}
		<p><em>{t}currently not available{/t}</em></p>
{/if}
	</div>
	<div class="buffer" style="width: 500px;">&nbsp;</div>
{/if}

{* next events *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/cacheicon/event.gif" style="align: left; margin-right: 10px;" width="24" height="24" alt="" />
		{t 1=$usercountry|escape}The next events in %1{/t}
	</p>
</div>
{include file="res_newevents.tpl" events=$events}

{* new logpix *}
<div class="content2-container bg-blue02" style="margin-bottom:6px">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/cacheicon/webcam.gif" style="align: left; margin-right: 10px;" width="24" height="24" alt="{t}News{/t}" />
		<a href="newlogpics.php" style="color:rgb(88,144,168); text-decoration: none;">{t}New log pictures{/t}</a>
	&nbsp; <span style="color:black; font-size:0.8em; font-weight:normal">[<a href="newlogpics.php">{t}more{/t}...</a>]</span>
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
{include file="res_newratings.tpl" topratings=$topratings}

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
{if !$rsserror}
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
	&nbsp; <span style="color:black; font-size:0.8em; font-weight:normal">[<a href="newcaches.php">{t}more{/t}...</a>]</span>
	</p>
</div>
<p style="line-height: 1.6em;">({t 1=$count_hiddens 2=$count_founds 3=$count_users}Total of %1 active Caches and %2 founds by %3 users{/t})</p>
{include file="res_newcaches.tpl" newcaches=$newcaches}
	

