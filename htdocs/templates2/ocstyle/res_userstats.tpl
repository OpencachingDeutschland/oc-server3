{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}

	<tr>
		<td valign="middle" class="header-small" style="padding-top:5px;padding-bottom:5px;white-space:nowrap" width="1%">
		<img src="resource2/{$opt.template.style}/images/cacheicon/22x20-traditional.png" width="22" height="20" align="middle" border="0" style="padding-right:2px" />&nbsp;<b>{t}Hidden caches:{/t}</b></td>
		<td class="header-small">{$hidden}
			{if $hidden>0}&nbsp;[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;ownerid={$userid}&amp;searchbyowner=&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{t}Show{/t}</a>]{if $hidden_active<$hidden}, &nbsp;{t 1=$hidden_active}%1 of these are active{/t} {if $hidden_active>0}&nbsp;[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=1&amp;output=HTML&amp;sort=byname&amp;ownerid={$userid}&amp;searchbyowner=&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{t}Show{/t}</a>]{/if}{/if}{/if}
		</td>
	</tr>

	{if $show_statistics==true}
		{if $hidden_by_cachetype|@count}
			<tr>
				<td style="padding-left:34px; vertical-align:top; line-height:1.8em">{t}... by cache type:{/t}</td>
				<td>
					<div style="padding-left:6px">
						{include file="res_cachetypestats.tpl" stat=$hidden_by_cachetype total=$hidden logs=false}
					</div>
				</td>
			</tr>
		{/if}
		{if $hidden_by_cachetype|@count || $found_by_cachetype|@count}
			<tr><td class="spacer" colspan="2"></td></tr>
		{/if}
	{/if}

	<tr>
		<td valign="middle" class="header-small" style="padding-top:5px;padding-bottom:5px;white-space:nowrap" width="1%">
			<img src="resource2/ocstyle/images/log/16x16-found.png" style="padding-right:2px"  />
			&nbsp;<b>{t}Caches found:{/t}</b>
		</td>
		<td class="header-small">
			{$found}
			{if $found>0}&nbsp;[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=1,7&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{t}Show{/t}</a>]{/if}
		</td>
	</tr>

	{if $show_statistics==true}
		{if $found_by_cachetype|@count}
			<tr>
				<td style="padding-left:34px; vertical-align:top; line-height:1.8em">{t}... by cache type:{/t}</td>
				<td>
					<div style="padding-left:6px">
						{include file="res_cachetypestats.tpl" stat=$found_by_cachetype total=$found logs=true}
					</div>
				</td>
			</tr>

			<tr>
				<td valign="top" style="padding-left:34px">
					{t}... by region:{/t}
				</td>
				<td>
					<table cellspacing="0" cellpadding="0">
					{foreach from=$regionstat item=region name=regions}
						<tr>
							<td class="default" style="text-align:right; padding:0">
								{$region.count} &nbsp;
							</td>
							<td class="default" style="padding:0">
								{if !$region.state}<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=1,7&amp;country={$region.countrycode}&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{$region.country}</a>{else}{$region.country}{/if}
								 {if $region.state}&gt; <a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=1,7&amp;adm2={$region.adm2code}&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{$region.state}</a>{/if}
								&nbsp;<img src="images/flags/{$region.countrycode|lower}.gif" />
							</td>
						</tr>
					{/foreach}
					</table>
				</td>
			</tr>
		{/if}
		{if $hidden_by_cachetype|@count || $found_by_cachetype|@count}
			<tr><td class="spacer" colspan="2"></td></tr>
		{/if}

		<tr>
			<td valign="middle" class="header-small" style="padding-top:5px;padding-bottom:5px;white-space:nowrap">
			<img src="resource2/ocstyle/images/log/16x16-dnf.png" />&nbsp;&nbsp;&nbsp;<b>{t}Not found{/t}{t}#colonspace#{/t}:</b></td>
			<td class="header-small" >{$dnf}
				{if $dnf > 0}&nbsp;[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=2&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{t}Show{/t}</a>]{/if}
			</td>
		</tr>
		<tr>
			<td valign="middle" class="header-small" style="padding-bottom:5px;white-space:nowrap">
			<img src="resource2/ocstyle/images/log/16x16-note.png" />&nbsp;&nbsp;&nbsp;<b>{t}Notes{/t}{t}#colonspace#{/t}:</b></td>
			<td class="header-small" >{$notes}
				{if $notes>0}&nbsp;[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=3&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{t}Show{/t}</a>]{/if}
			</td>
		</tr>
		{if $maintainence > 0}
		<tr>
			<td valign="middle" class="header-small" style="padding-bottom:5px;white-space:nowrap">
			<img src="resource2/ocstyle/images/viewcache/16x16-maintenance.png" />&nbsp;&nbsp;&nbsp;<b>{t}Maintenance logs{/t}{t}#colonspace#{/t}:</b></td>
			<td class="header-small" >{$maintainence}
				&nbsp;[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=9,10,11,13,14&amp;calledbysearch=0{if $oconly}&amp;cache_attribs=6{/if}">{t}Show{/t}</a>]
			</td>
		</tr>
		{/if}
	{/if}

	<tr>
		<td valign="middle" class="header-small" style="padding-bottom:5px;white-space:nowrap">
		<img src="resource2/{$opt.template.style}/images/viewcache/rating-star.gif" align="middle" border="0" style="padding-right:2px" />&nbsp;&nbsp;<b>{t}Recommendations{/t}{t}#colonspace#{/t}:</b></td>
		<td class="header-small" >{if $maxrecommended !== null}{t 1=$recommended 2=$maxrecommended}%1 of %2 possibles{/t}{else}{$recommended}{/if} {if $recommended>0}&nbsp;[<a href="usertops.php?userid={$userid}{if $oconly}&oconly=1{/if}">{t}Show{/t}</a>]{/if}
		</td>
	</tr>

	{if $show_picstat && $logpics !== null}
		<tr>
			<td class="header-small">
			<img src="resource2/{$opt.template.style}/images/action/16x16-addimage.png" align="middle" border="0" />&nbsp;&nbsp;&nbsp;<b>{t}Log pictures{/t}{t}#colonspace#{/t}:</b></td>
			<td class="header-small" >{$logpics} {if $logpics>0}&nbsp;[<a href="viewprofile.php?userid={$userid}&allpics=1">{t}Show{/t}</a>]{/if}
			</td>
		</tr>
	{/if}
