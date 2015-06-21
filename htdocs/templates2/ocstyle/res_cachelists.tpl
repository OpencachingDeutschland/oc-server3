{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}

	<table class="null" border="0" cellspacing="0" width="98%">
		<tr>
			<td colspan="2">
				<table class="table">
					<tr class="cachelistheader">
						<th width="{if $show_watchers}330px{else}360px{/if}">{t}Cache list{/t}</th>
						{if $show_user}<th width="{if $show_watchers}130px{else}160px{/if}">{t}by{/t}</th>{/if}
						{if $show_status}<th width="70px">{t}Status{/t}</th>{/if}
						<th width="50px">{t}Caches{/t}</th>
						{if $show_watchers}<th width="60px">{t}Watchers{/t}</th>{/if}
						{if $show_edit || ($togglewatch && $login.userid)}<th width="{if $show_edit}160px{else}140px{/if}"></th>{/if}
					</tr>
					{foreach from=$cachelists item=cachelist}
						{cycle assign=listcolor values="listcolor1,listcolor2"}
						<tr class="cachelistitem">
							<td class="{$listcolor}">{include file="res_cachelist_link.tpl"}</td>
							{if $show_user}<td class="{$listcolor}"><a href="viewprofile.php?userid={$cachelist.user_id}">{$cachelist.username|escape}</a></td>{/if}
							{if $show_status}<td class="{$listcolor}">{if $cachelist.is_public}{t}public{/t}{else}{t}private{/t}{/if}</td>{/if}
							<td class="{$listcolor}" style="text-align:center">{$cachelist.entries}</td>
							{if $show_watchers}<td class="{$listcolor}" style="text-align:center">{if $cachelist.watchers}{$cachelist.watchers}{/if}</td>{/if}
							{if $show_edit || ($togglewatch && $login.userid)}
							<td class="{$listcolor}" style="text-align:right" >
								{if $show_edit}[<a href="mylists.php?edit={$cachelist.id}">{t}edit{/t}</a>]&nbsp;[<a href="" onclick="javascript:if(confirm('{t 1=$cachelist.name escape=js}Do you really want to delete the list \'%1\'?{/t}'))location.href='mylists.php?delete={$cachelist.id}'">{t}delete{/t}</a>]{/if}
								{if $togglewatch}<nobr>[<a href="{if $cachelist.watched_by_me && $removewatch_confirm}javascript:if(confirm('{t escape=js}Do you really want to delete this entry?{/t}'))location.href='{$togglewatch}?dontwatchlist={$cachelist.id}'{else}{$togglewatch}?{if $cachelist.watched_by_me}dont{/if}watchlist={$cachelist.id}{/if}">{if $cachelist.watched_by_me}{t}do not watch{/t}{else}{t}watch{/t}{/if}</a>]</nobr>{/if}
							</td>
							{/if}
						</tr>
					{foreachelse}
						<tr><td colspan="{$show_user + $show_status + 4}"><br />{t}There are no lists yet.{/t}</td></tr>
					{/foreach}
				</table>
			</td>
		</tr>
	</table>
