{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="" />
	{t}Ignored Geocaches{/t}
</div>

<table class="null" border="0" cellspacing="0" width="98%">
	<tr>
		<td colspan="2">
			<table class="table">
				<tr class="searchresult">
					<td width="50px"><b>{t}Type{/t}</b></td>
					<td width="50px"><b>{t}State{/t}</b></td>
					<td width="500px"><b>{t}Name{/t}</b></td>
					<td width="200px">&nbsp;</td>
				</tr>
				{foreach from=$ignores item=ignoreItem}
					{cycle values="#eeeeee,#e0e0e0" assign=bgcolor}
					<tr>
						<td style="border-bottom: solid 1px grey;">{include file="res_cacheicon_22.tpl" cachetype=$ignoreItem.type|escape}</td>
						<td style="border-bottom: solid 1px grey;">{include file="res_cachestatus.tpl" status=$ignoreItem.status}</td>
						<td style="border-bottom: solid 1px grey;"><span style="{include file="res_cachestatus_span.tpl" status=$ignoreItem.status}"><a href="viewcache.php?wp={$ignoreItem.wp}">{$ignoreItem.name|escape}</a></span></td>
						<td style="border-bottom: solid 1px grey;">[<a href="javascript:if(confirm('{t escape=js}Do you really want to delete this entry?{/t}'))location.href='ignore.php?cacheid={$ignoreItem.cacheid}&action=removeignore'">{t}remove{/t}</a>]</td>
					</tr>
				{foreachelse}
					<tr><td colspan="4">{t}No ignored Geocaches found.{/t}</td></tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
