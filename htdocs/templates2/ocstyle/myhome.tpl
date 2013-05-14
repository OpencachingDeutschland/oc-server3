{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

{* Welcome *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" border="0" width="32px" height="32px" style="align: left; margin-right: 10px;" />
	{t 1=$login.username}Hello %1{/t}
</div>

{if !$allpics}
	{* Geocaches found *}
	<div class="content2-container bg-blue02" style="margin-top:20px;">
		<p class="content-title-noshade-size3">
			<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" width="22" height="22"  style="align: left; margin-right: 10px;" />&nbsp;
			{t 1=$found}Finds: %1{/t} &nbsp;
			<span class="content-title-link">[<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bymylastlog&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=1,7">{t}Geocaches found{/t}</a>]&nbsp; [<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=bymylastlog&amp;finderid={$login.userid}&amp;searchbyfinder=&amp;f_inactive=0&amp;logtype=1,2,3,6,7,8">{t}Geocaches logged{/t}</a>]</span>
		</p>
	</div>

	{* Ocprop: (find|us|own)erid=([0-9]+) *}
	<p style="line-height: 1.6em;"><b>{t}Your latest log entries:{/t}</b></p>

	<table class="table">
		{foreach from=$logs item=logItem}
			<tr>
				<td>{include file="res_logtype.tpl" type=$logItem.type}</td>
				<td>{$logItem.date|date_format:$opt.format.datelong}</td>
				<td><a href="viewcache.php?wp={$logItem.wp_oc}">{$logItem.name|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$logItem.userid}">{$logItem.username|escape}</a></td>
			</tr>
		{foreachelse}
			<tr><td>{t}No entries found{/t}</td></tr>
		{/foreach}
	</table>
{/if}

{* Log pictures *}
<div class="content2-container bg-blue02" style="margin-top:20px;">
	<p class="content-title-noshade-size3">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-pictures.gif" width="24" height="24"  style="align: left; margin-right: 10px;" />&nbsp;
		{t 1=$total_pictures}Log pictures: %1{/t} &nbsp;
		{if !$allpics}<span class="content-title-link">[<a href="myhome.php?allpics=1">{t}Show all{/t}</a>]</span>{/if}
	</p>
</div>

{if $pictures|@count == 0}
	<p>{t}You did not upload any log pictures yet.{/t}</p>
{else}
	<p style="line-height: 1.6em;">
		{if !$allpics}
			 <b>{t}Your latest log pictures{/t}:</b></p>
			{assign var=maxlines value=1}
		{else}
			{assign var=subtitle value="{t}Your log pictures{/t}:"}
			{assign var=maxlines value=0}
		{/if}
	</p>

	{include file="res_logpictures.tpl" logdate=true loguser=false maxlines=$maxlines fullyear=true}

	{if $allpics}
		<p>{t}In your <a href="mydetails.php">profile settings</a> you can choose if your log pictures stat and gallery is visible for other users.{/t}</p>
	{/if}
{/if}

{if !$allpics}
	{* Geocaches hidden *}
	<div class="content2-container bg-blue02" style="margin-top:5px;">
		<p class="content-title-noshade-size3">
			<img src="resource2/{$opt.template.style}/images/misc/22x22-traditional.gif" width="22" height="22"  style="align: left; margin-right: 10px;" />&nbsp;
			{t 1=$hidden}Geocaches hidden: %1{/t} &nbsp;
			<span class="content-title-link">[<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=0">{t}Show all{/t}</a>]&nbsp; [<a href="search.php?showresult=1&amp;expert=0&amp;output=HTML&amp;sort=byname&amp;ownerid={$login.userid}&amp;searchbyowner=&amp;f_inactive=1">{t}Show active{/t}</a>]</span>
		</p>
	</div>

	<p style="line-height: 1.6em;">
	{* Ocprop: (find|us|own)erid=([0-9]+) *}
		<b>{t}Your latest Geocaches hidden:{/t}</b>
	</p>

	<table class="table">
		{foreach from=$caches item=cacheItem}
			<tr>
				<td>{include file="res_cachestatus.tpl" status=$cacheItem.status}</td>
				<td nowrap="nowrap">{$cacheItem.date_hidden|date_format:$opt.format.datelong}</td>
				<td><a href="viewcache.php?wp={$cacheItem.wp_oc}">{$cacheItem.name|escape}</a></td>
			</tr>
		{foreachelse}
			<tr><td>{t}No Geocaches hidden{/t}</td></tr>
		{/foreach}
	</table>

	{* ... not published caches *}
	<p style="line-height: 1.4em; margin-top:12px;"><b>{t}Not published Geocaches will be available{/t}</b></p>

	<table class="table">
		{foreach from=$notpublished item=notpublishedItem}
			<tr>
				<td>{include file="res_cachestatus.tpl" status=$notpublishedItem.status}</td>
				<td>{$notpublishedItem.date_activate|date_format:$opt.format.datelong}</td>
				<td><a href="viewcache.php?wp={$notpublishedItem.wp_oc}">{$notpublishedItem.name|escape}</a></td>
			</tr>
		{foreachelse}
			<tr><td>{t}All Geocaches are published{/t}</td></tr>
		{/foreach}
	</table>

	{* Other information *}
	{*
	<div class="content2-container bg-blue02" style="margin-top:20px;">
		<p class="content-title-noshade-size3">
			<img src="resource2/{$opt.template.style}/images/misc/25x25-world.png" width="25" height="25" style="align: left; margin-right: 10px;" />&nbsp;
			{t}Other information{/t}
		</p>
	</div>
	*}

	{* Emails sent *}
	{* useless information when email protocol is regularly cleand-up
	<p>
		<img src="resource2/{$opt.template.style}/images/misc/22x22-email.png" width="22" height="22" style="align: left; margin-right: 10px;" />&nbsp;
		<strong>{t 1=$emails}E-Mails sent: %1{/t}</strong>
	</p>
	*}
{/if}

<div class="buffer">&nbsp;</div>

