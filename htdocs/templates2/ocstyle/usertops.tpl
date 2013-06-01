{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Recommendations{/t}" />
	{t 1=$userid 2=$username|escape}Recommendations of <a href="viewprofile.php?userid=%1">%2</a>{/t}
</div>

<table class="table">
	<tr>
		<td colspan="2">
			<table class="table" border="0" cellspacing="0" width="100%">
				<tr class="searchresult">
					<th width="30px">{t}Type{/t}</th>
					<th width="620px">{t}Name{/t}</th>
					<th width="50px">{t}State{/t}</th>
				</tr>
				{foreach from=$ratings item=ratingItem}
					{cycle assign=listcolor values="listcolor1,listcolor2"}
					<tr>
						<td class="{$listcolor}">{include file="res_cacheicon_22.tpl" cachetype=$ratingItem.type|escape}</td>
						<td class="{$listcolor}"><span style="{include file="res_cachestatus_span.tpl" status=$ratingItem.status}"><a href="viewcache.php?cacheid={$ratingItem.cacheid}">{$ratingItem.cachename|escape}</a></span>&nbsp;{t 1=$ratingItem.ownername|escape}by %1{/t}</td>
						<td class="{$listcolor}">{include file="res_cachestatus.tpl" status=$ratingItem.status}</td>
					</tr>
				{foreachelse}
					<tr><td colspan="2"><br />{t 1=$username|escape}%1 has not recommended any geocache.{/t}</td></tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
