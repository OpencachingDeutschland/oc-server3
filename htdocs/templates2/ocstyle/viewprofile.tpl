{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" style="align: left; margin-right: 10px;" width="32" height="32" />
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
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="align: left; margin-right: 10px;" width="32" height="32"  /> 
		{t}User data{/t}
	</p>
</div>

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>

	{if $showcountry==true}
		<tr>
			<td><b>{t}Country{/t}:</b></td>
			<td>{$country|escape}</td>
		</tr>
	{/if}

	<tr>
		<td><b>{t}Registered&nbsp;since{/t}:</b></td>
		<td>{$registered|date_format:$opt.format.date}</td>
	</tr>
	<tr>
		<td style="vertical-align:top"><b>{t}Last&nbsp;login{/t}:</b></td>
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
				<td style="vertical-align:top;"><b>{$optionItem.name|escape}:</b></td>
				<td style="vertical-align:top;">{$optionItem.option_value|escape|nl2br}</td>
			{/if}
		</tr>
	{/foreach}

	{if $pmr==1}
		<tr>
			<td style="vertical-align:top;"><b>{t}Others{/t}:</b></td>
			<td>
				{t}I'm taking an PMR radio on channel 2 with me{/t}<br />
			</td>
		</tr>
	{/if}

	<tr><td><td class="spacer"> </td></tr>
</table>

{* description *}
{if $description != ""}
	<div class="content2-container bg-blue02" >
	</div>
	<div class="table-like-font" style="margin-left:6px">
		{$description}
	</div>
	<div>&nbsp;</div>
{/if}

{* all-caches statistics *}
<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/cacheicon/20x20-3.png" style="align: left; margin-right: 10px;" />
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
		<img src="resource2/{$opt.template.style}/images/misc/is_oconly_small.png" style="align: left; margin-right: 10px;" />
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

	<tr><td class="spacer"></td></tr>

	{if $show_oconly81}
		<tr><td colspan="3">
		<table class="stattable">
			<tr>
				<th class="h1"></th>
				<th class="h1" colspan="11" style="text-align:center; line-height:1.8em">{t}Terrain{/t}</th>
			</tr>
			<tr>
				<td></td>
				<td>&nbsp;<img src="resource2/ocstyle/images/log/16x16-found.png" /</td>
				{foreach from=$stat81 key=step item=dummy}
					<th style="text-align:center">{$step/2}</th>
				{/foreach}
				<th class="h1" style="text-align:center">Σ</th>
			</tr>
			{assign var=matrixfound value=0}
			{foreach from=$stat81 key=difficulty item=terrains name=difficulty}
				<tr>
					{if $smarty.foreach.difficulty.first}
						<th class="h1" rowspan="9">{t}Difficulty{/t}&nbsp;&nbsp;&nbsp;&nbsp;</th>
					{/if}
					<th>&nbsp;{$difficulty/2}</th>
					{assign var=dsum value=0}
					{foreach from=$terrains key=terrain item=count}
						<td style="text-align:center; background-color:{if $count}rgb({$count/$stat81_maxcount*-242+242.5|floor},{$count/$stat81_maxcount*-242+242.5|floor},242){else}#f2f2f2{/if}" {if $count}onclick='location.href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=1,7&amp;calledbysearch=0&amp;cache_attribs=6&amp;terrainmin={$terrain}&amp;terrainmax={$terrain}&amp;difficultymin={$difficulty}&amp;difficultymax={$difficulty}"'{/if}>
							{if $count}
								<span style="cursor:pointer; color:{if $count > $stat81_maxcount/3}#fff{else}#000{/if}">{$count}</span>
								{assign var=dsum value=$dsum+$count}
								{assign var=matrixfound value=$matrixfound+1}
							{else}&nbsp;{/if}
						</td>
					{/foreach}
					<td style="text-align:center">{if $dsum}{$dsum}{/if}</td>
				</tr>
			{/foreach}
			<tr>
				<td rowspan="2"></td>
				<th class="h1" style="text-align:center">Σ</th>
				{foreach from=$stat81_tsum item=count}
					<td style="text-align:center">{if $count}{$count}{/if}</td>
				{/foreach}
				<td style="text-align:center"><b>{$oconly_found}</b></td>
			</tr>
			<tr>
				<td colspan="11" style="padding-top:0.5em"><p>{t 1=$matrixfound}The user has found %1 of 81 theoretically possible terrain/difficulty combinations.{/t}</p></td> 
			</tr>
		</table>
		</td></tr>
	{/if}
</table>
