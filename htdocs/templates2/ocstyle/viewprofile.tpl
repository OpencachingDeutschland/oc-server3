{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{* JS for cache list description tooltips *}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" style="margin-right: 10px;" width="32" height="32" />
	{t 1=$username}Userprofile of %1{/t}
</div>

{* send email *}
<div class="default" style="text-align: right;padding-right: 22px;">
	<a href="mailto.php?userid={$userid}"><img src="resource2/{$opt.template.style}/images/misc/16x16-email.png" width="16" height="16" border="0" alt="{t}Send E-Mail{/t}" align="middle" /></a>&nbsp;
	[<a href="mailto.php?userid={$userid}">{t}Send E-Mail{/t}</a>]
</div>

<div class="buffer" style="width: 500px;">&nbsp;</div>

{* profile data *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/profile/32x22-profile.png" style="margin-right: 10px;" width="32" height="22"  /> 
		{t}User data{/t}
	</p>
</div>

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>

	{if $showcountry==true}
		<tr>
			<td><b>{t}Country:{/t}</b></td>
			<td>{$country|escape}</td>
		</tr>
	{/if}

	<tr>
		<td><b>{t}Registered&nbsp;since:{/t}</b></td>
		<td>{$registered|date_format:$opt.format.date}</td>
	</tr>
	<tr>
		<td style="vertical-align:top"><b>{t}Last&nbsp;login:{/t}</b></td>
		{if $lastlogin==1}
			<td>{t}Within the last month{/t}</td>
		{elseif $lastlogin==2}
			<td>{t}Between 1 and 6 months{/t}</td>
		{elseif $lastlogin==3}
			<td>{t}Between 6 and 12 months{/t}</td>
		{elseif $lastlogin==4}
			<td>{t}More than 12 months ago{/t}</td>
		{elseif $lastlogin==6}
			{if $license_actively_declined}
				<td>{t}The user account has been disabled, because the user declined the <a href="articles.php?page=impressum#datalicense">data license</a>. Cache descriptions, log texts and pictures have been deleted.{/t}</td>
			{elseif $license_passively_declined}
				<td>{t}The user account has been disabled.{/t} {t}Cache descriptions, log texts and pictures have been deleted, because the account was disabled before transition to the <a href="articles.php?page=impressum#datalicense">new data license</a>.{/t}</td>
			{else}
				<td>{t}The user account has been disabled.{/t}</td>
			{/if}
		{else}
			<td>{t}unknown{/t}</td>
		{/if}
	</tr>

	{foreach from=$useroptions item=optionItem}
		<tr>
			{if $optionItem.option_id != 3}
				<td style="vertical-align:top;"><b>{$optionItem.name|escape}{t}#colonspace#{/t}:</b></td>
				<td style="vertical-align:top;">{$optionItem.option_value|escape|nl2br}</td>
			{/if}
		</tr>
	{/foreach}

	{if $pmr==1}
		<tr>
			<td style="vertical-align:top;"><b>{t}Others:{/t}</b></td>
			<td>
				{t}I'm taking an PMR radio on channel 2 with me{/t}<br />
			</td>
		</tr>
	{/if}

	<tr><td><td class="spacer"></td></tr>
</table>

{* description *}
{if $description != ""}
	<div class="content2-container bg-blue02" style="height:0px"></div>
	<div class="content2-container table-like-font">
		<div style="margin-left:6px">
			{$description}
		</div>
	</div>
{/if}
<div>&nbsp;</div>

{* all-caches statistics *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/cacheicon/20x20-3.png" style="margin-right: 10px;" />
		{t}User statistics{/t}
	</p>
</div>

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>

	{include file="res_userstats.tpl"
	         oconly=false
	         hidden=$hidden
	         hidden_active=$active
	         hidden_by_cachetype=$userstatshidden
	         found=$founds
	         found_by_cachetype=$userstatsfound
	         dnf=$notfound
	         notes=$note
	         maintainence=$maintenance
	         recommended=$recommended
	         maxrecommended=$maxrecommended
	         logpics=$logpics
	         regionstat=$regionstat}

	<tr><td class="spacer">&nbsp;</td></tr>
</table>

{* OConly statistics *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/misc/40x22-oconly.png" style="margin-right: 10px;" />
		{t}OConly statistics{/t}
	</p>
</div>

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>

	{include file="res_userstats.tpl"
	         oconly=true
	         hidden=$oconly_hidden
	         hidden_active=$oconly_hidden_active
	         hidden_by_cachetype=$oconly_userstatshidden
	         found=$oconly_found
	         found_by_cachetype=$oconly_userstatsfound
	         dnf=$oconly_dnf
	         notes=$oconly_note
	         maintainence=$oconly_maint
	         recommended=$oconly_recommended
	         maxrecommended=null
	         logpics=$oconly_logpics
	         regionstat=$oconly_regionstat}

	{if $show_oconly81}
		<tr><td class="spacer"></td></tr>
		<tr><td class="spacer"></td></tr>
		<tr id="oconly81">
			<td colspan="3">
				{include file="res_oconly81.tpl" userid=$userid}
			</td>
		</tr>
	{else}
		<tr>
			<td valign="middle" class="header-small" style="white-space:nowrap" width="1%">
				<img src="resource2/ocstyle/images/log/16x16-found.png" style="padding-right:2px"  />
				&nbsp;<b>{t}OConly-81 statistics:{/t}</b>
			</td>
			<td valign="middle">
				{t}inactive{/t} (<a href="oconly81.php">{t}Info{/t}</a>)
			</td>
		</tr>
		<tr><td class="spacer"></td></tr>
		<tr><td class="spacer"></td></tr>
	{/if}
	<tr><td class="spacer"></td></tr>
	<tr><td class="spacer"></td></tr>

</table>


{* personal cache lists *}
{if $cachelists|@count}
	<div class="content2-container bg-blue02" id="cachelists">
		<p class="content-title-noshade-size2">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-list.png" style="margin-right: 10px; height:22px" />
		{t}Cache lists{/t}
		</p>
	</div>
	<p>
		<ul class="default">
			{foreach from=$cachelists item=cachelist}
				<li>
					{include file="res_cachelist_link.tpl"} &nbsp;
					{if $login.userid}[<a href="viewprofile.php?userid={$userid}&{if $cachelist.watched_by_me}dont{/if}watchlist={$cachelist.id}&dummy={$tdummy}#eocl">{if $cachelist.watched_by_me}{t}do not watch{/t}{else}{t}watch{/t}{/if}</a>]{/if}
				</li>
			{/foreach}
		</ul>
		<br id="eocl" />
	</p>
{/if}
