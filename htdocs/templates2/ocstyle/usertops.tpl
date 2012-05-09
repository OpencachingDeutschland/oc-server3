{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Recommendations{/t}" />
	{t 1=$userid 2=$username|escape}Recommendations of <a href="viewprofile.php?userid=%1">%2</a>{/t}
</div>

<table class="table">
	<tr>
		<td colspan="2">
			<table class="null" border="0" cellspacing="0" width="100%">
				<tr class="searchresult">
					<td width="30px"><b>{t}Type{/t}</b></td>
					<td width="620px"><b>{t}Name{/t}</b></td>
					<td width="50px"><b>{t}State{/t}</b></td>
				</tr>
				{foreach from=$ratings item=ratingItem}
					<tr>
						<td style="border-bottom: solid 1px grey;">{include file="res_cacheicon_22.tpl" cachetype=$ratingItem.type|escape}</td>
						<td style="border-bottom: solid 1px grey;">{t 1=$ratingItem.cacheid 2=$ratingItem.cachename|escape 3=$ratingItem.ownername|escape}<a href="viewcache.php?cacheid=%1">%2</a> by %3{/t}</td>
						<td style="border-bottom: solid 1px grey;">{include file="res_cachestatus.tpl" status=$ratingItem.status}</td>
					</tr>
				{foreachelse}
					<tr><td colspan="2">{t 1=$username|escape}%1 has not recommended any geocache.{/t}</td></tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
