{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

<table class="table" width="98%">
	{if $reports|@count}
	<tr>
		<th>{t}ID{/t}</th>
		<th>{t}Report date{/t}</th>
		<th>{t}Reporter{/t}</th>
		<th>{t}Reason{/t}</th>
		<th>{t}Admin{/t}</th>
		<th>{t}Last modified{/t}</th>
		<th>{t}Status{/t}</th>
	</tr>

	{foreach from=$reports item=report}
		<tr>
			<td><a href="adminreports.php?id={$report.id}">{$report.id}</a></td>
			<td>{$report.date_created|date_format:$opt.format.date}</td>
			<td><a href="viewprofile.php?userid={$report.userid}">{$report.usernick}</a></td>
			<td>{$report.reason}</td>
			<td><a href="viewprofile.php?userid={$report.adminid}">{$report.adminnick}</a></td>
			<td>{$report.lastmodified|date_format:$opt.format.date}</td>
			<td>{$report.status}</td>
		</tr>
	{/foreach}

	{else}
		<tr><td></td></tr>
	{/if}
</table>

