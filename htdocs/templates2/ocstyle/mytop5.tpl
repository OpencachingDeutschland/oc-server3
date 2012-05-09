{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="Recommendations" />
	{t}My recommendations{/t}
</div>
  
{if $deleted==true}		  
	<div class="content-txtbox-noshade">
		<p style="line-height: 1.6em;"><span style="color: red">{t 1=$deletedItem.wp 2=$deletedItem.cachename|escape}Your recommendation for "<a href="viewcache.php?wp=%1">%2</a>" has been removed!{/t}</span></p>
		<div class="buffer" style="width: 500px;">&nbsp;</div>
	</div> 
{/if}

<table class="table">
	<tr>
		<td>
			<table class="null" border="0" cellspacing="0" width="100%">
				<tr class="searchresult">
					<td width="50px"><b>{t}Type{/t}</b></td>
					<td width="50px"><b>{t}State{/t}</b></td>
					<td width="500px"><b>{t}Name{/t}</b></td>
					<td width="200px">&nbsp;</td>
				</tr>
				{foreach from=$ratings item=ratingItem}
					{cycle values="#eeeeee,#e0e0e0" assign=bgcolor}
					<tr>
						<td style="border-bottom: solid 1px grey;">{include file="res_cacheicon_22.tpl" cachetype=$ratingItem.type|escape}</td>
						<td style="border-bottom: solid 1px grey;">{include file="res_cachestatus.tpl" status=$ratingItem.status}</td>
						<td style="border-bottom: solid 1px grey;"><span style="{include file="res_cachestatus_span.tpl" status=$ratingItem.status}"><a href="viewcache.php?wp={$ratingItem.wp}">{$ratingItem.cachename|escape}</a></span></td>
						<td style="border-bottom: solid 1px grey;">[<a href="javascript:if(confirm('{t escape=js}Do you really want to remove this recommendation?{/t}'))location.href='mytop5.php?action=delete&amp;cacheid={$ratingItem.cacheid}'">{t}Remove recommendation{/t}</a>]</td>
					</tr>
					<tr><td class="spacer" colspan="4"></td></tr>
				{foreachelse}
					<tr><td colspan="2">{t}You haven't recommended a Geocache.{/t}</td></tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
