{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
		{t}Status changes{/t} <small>{t}since June 2013{/t}</small>
	</p>
</div>

<table class="table" width="80%">
	{if $status_changes|@count}
	<tr>
		<th>{t}Date{/t}</th>
		<th>{t}Status{/t}</th>
		<th>{t}Changed by{/t}</th>
	</tr>
	{foreach from=$status_changes item=change}
		<tr>
			<td>{$change.date_modified|date_format:$opt.format.date}</td>
			<td>{$change.old_status} &rarr; {$change.new_status} {include file="res_cachestatus.tpl" status=$change.new_status_id}</td>
			<td><a href="viewprofile.php?userid={$change.userid}">{$change.username}</a></td>
		</tr>
	{/foreach}
	{else}
		<tr><td></td></tr>
	{/if}
</table>
