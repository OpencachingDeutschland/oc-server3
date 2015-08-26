{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png" style="margin-right: 10px;" width="32" height="32" alt="{t}Special caches{/t}" />
	{t}Special caches{/t}
</div>

<table class="table">
	<tr>
		<td style="padding-bottom:20px;">
			{t 1=$helppagelink 2=$opt.template.style}The following list is generated automatically by the given recommendations of the users. You can find more informations on
			regional classification in the %1Wiki</a>.<br />
			<br />
			The numbers in the list below means:<br />
			<img src="images/rating-star.gif" border="0" alt="Recommendations" /> Number of users that recommend this cache<br />
			<img src="resource2/%2/images/log/16x16-found.png" width="16" height="16" border="0" alt="Found" /> Checks = Number of time the cache was found<br />
			Index tries to take the number of recommendations and founds in an order to show 'the best' geocache first.<br />
			<img src="images/tops-formula.png" border="0" alt="Formula" />{/t}
		</td>
	</tr>
	<tr>
		<td style="padding-left:32px; padding-bottom:32px;">
			<table class="narrowtable" width="100%">
				{assign var='lastadm1' value=''}
				{assign var='lastadm3' value=''}

				{foreach name=tops from=$tops item=topItem}
					{if ($lastadm1!=$topItem.adm1 || ($lastadm3!=$topItem.adm3))}
						<tr>
							<td valign="top" width="150px" {if $lastadm1!=$topItem.adm1}style="padding-top:12px"{/if}>
								{if $lastadm1!=$topItem.adm1}
									{$topItem.adm1|escape}&nbsp; <img src="images/flags/{$topItem.code1|lower}.gif" />
								{/if}
							</td>
							<td {if $lastadm1!=$topItem.adm1}style="padding-top:12px"{/if}>
								{if $topItem.adm3==null}
									<a href="#{$topItem.adm1|urlencode}null"><i>({t}without regional reference{/t})</i><br /></a>
								{else}
									<a href="#{$topItem.adm1|urlencode}{$topItem.adm3|urlencode}">{$topItem.adm3|escape}</a><br />
								{/if}
							</td>
						</tr>
					{/if}

					{assign var='lastadm1' value=$topItem.adm1}
					{assign var='lastadm3' value=$topItem.adm3}
				{/foreach}
			</table>
		</td>
	</tr>
</table>

{assign var='lastItem' value=false}
{assign var='makehead' value=true}
{assign var='makefoot' value=false}

{foreach name=tops from=$tops item=topItem}
	{if $smarty.foreach.tops.first}
		{assign var='makehead' value=true}
	{elseif ($topItem.adm1!=$lastItem.adm1) || ($topItem.adm3!=$lastItem.adm3)}
		{assign var='makehead' value=true}
		</table>
	{/if}

	{if $makehead}
		{if !$smarty.foreach.tops.first}<br />{/if}
		<p class="content-title-noshade-size3">
			<a name="{$topItem.adm1|urlencode}{if $topItem.adm3==null}null{else}{$topItem.adm3|urlencode}{/if}"></a> 
			{$topItem.adm1|escape}
			&gt;
			{if $topItem.adm3==null}
				({t}without regional reference{/t})
			{else}
				{$topItem.adm3|escape}
			{/if}
		</p>

		<table class="table">
			<tr>
				<td style="text-align:right"><b>{t}Index{/t}</b></td>
				<td style="text-align:right"><img src="images/rating-star.gif" border="0" alt="{t}Recommendations{/t}" /></td>
				<td style="text-align:right"><img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" border="0" alt="{t}Found{/t}" /></td>
				<td></td>
				<td>&nbsp;</td>
			</tr>
	{/if}

	<tr>
		<td width="40px" style="text-align:right">
			{$topItem.idx}
		</td>
		<td width="30px" style="text-align:right">
			{$topItem.ratings}
		</td>
		<td width="40px" style="text-align:right">
			{$topItem.founds}
		</td>
		<td></td>
		<td>
			<a href="viewcache.php?wp={$topItem.wpoc}">{$topItem.name|escape}</a> {include file="res_oconly.tpl" oconly=$topItem.oconly} {t}by{/t} <a href="viewprofile.php?userid={$topItem.userid}">{$topItem.username|escape}</a>
		</td>
	</tr>

	{if $smarty.foreach.tops.last}
		{assign var='makefoot' value=true}
	{/if}
	{if $makefoot}
			<tr><td class="spacer">&nbsp;</td></tr>
		</table>
	{/if}

	{assign var='lastItem' value=$topItem}
	{assign var='makehead' value=false}
	{assign var='makefoot' value=false}
{/foreach}
