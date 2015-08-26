{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{if !$reportdisplay}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
	{t}Cache history{/t}
</div>

<form method="POST" action="adminhistory.php">
	<p>
		<b>{t}Cache code:{/t}</b> &nbsp;
		<input type="text" width="10" name="wp" /> &nbsp;
		<input type="submit" name="submitform" value="{t}Show{/t}" class="formbutton" onclick="submitbutton('submitform')" />
	</p>
</form>
{/if}

{if $showhistory}
	{if !$reportdisplay}
		<p>
			<a href="viewcache.php?cacheid={$cache.cache_id}">{$cache.name}</a>
			{t}by{/t}
			<a href="viewprofile.php?userid={$cache.user_id}">{$ownername}</a>
		</p>
		<br />
	{/if}

	{if !$reportdisplay || $reports|@count}
		<div class="content2-container bg-blue02">
			<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="22" height="22" alt="" /> 
				{if $reportdisplay}	{t}Other reports for this cache{/t}{else}{t}Cache reports{/t}{/if}
			</p>
		</div>

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
		<br />
	{/if}

	{if !$reportdisplay || $deleted_logs|@count}
		<div class="content2-container bg-blue02">
			<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/description/22x22-logs.png" style="margin-right: 10px;" width="22" height="22" alt="" /> 
				{t}Deleted logs{/t} <small>{t}since deletion date February 2012{/t}</small>
			</p>
		</div>
		<div class="content2-container">
			{include file="res_logentry.tpl" header=false footer=false footbacklink=false cache=$cache logs=$deleted_logs}
		</div>
		<p>&nbsp;</p>
	{/if}

	{if !$reportdisplay || $status_changes|@count}
		<div class="content2-container bg-blue02">
			<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/viewcache/16x16-maintenance.png" style="margin-right: 10px;" width="16" height="16" alt="" /> 
				{t}Status changes{/t} <small>{t}since June 2013{/t}</small>
			</p>
		</div>

		<table class="table" width="90%">
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
					<td><a href="viewprofile.php?userid={$change.user_id}">{$change.username}</a></td>
				</tr>
			{/foreach}
		{else}
			<tr><td></td></tr>
		{/if}
		</table>
		<p>&nbsp;</p>
	{/if}

	{if !$reportdisplay || $coordinates|@count > 1}
		<div class="content2-container bg-blue02">
			<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/description/20x20-compass.png" style="margin-right: 10px;" width="20" height="20" alt="" /> 
				{t}Coordinate changes{/t}
			</p>
		</div>

		<table class="table" width="90%">
		{if $coordinates|@count > 1}
			<tr>
				<th>{t}Date{/t}</th>
				<th>{t}Coordinates{/t}</th>
				<th>{t}Changed by{/t}</th>
			</tr>
			{assign var=n value=0}
			{foreach from=$coordinates item=coord}
				<tr>
					<td width="20%">{$coord.date|date_format:$opt.format.date}</td>
					<td>{$coord.coord.lat|escape} {$coord.coord.lon|escape}</td>
					{assign var=n value=$n+1}
					{if $n < $coordinates|@count}
						<td><a href="viewprofile.php?userid={$coord.user_id}">{$coord.username}</a></td>
					{/if}
				</tr>
			{/foreach}
		{else}
			<tr><td></td></tr>
		{/if}
		</table>
		<p>&nbsp;</p>
	{/if}

	{if !$reportdisplay || $adoptions|@count}
		<div class="content2-container bg-blue02">
			<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/profile/32x32-adopt.png" style="margin-right: 10px;" width="22" height="22" alt="" /> 
				{t}Adoptions_{/t} <small>{t}since September 2012{/t}</small>
			</p>
		</div>

		<table class="table" width="90%">
		{if $adoptions|@count}
			<tr>
				<th>{t}Date{/t}</th>
				<th>{t}From Owner{/t}</th>
				<th></th>
				<th>{t}To Owner{/t}</th>
			</tr>
			{foreach from=$adoptions item=adoption}
				<tr>
					<td>{$adoption.date|date_format:$opt.format.date}</td>
					<td><a href="viewprofile.php?userid={$adoption.from_user_id}">{$adoption.from_username}</a></th>
					<td>&rarr;</td>
					<td><a href="viewprofile.php?userid={$adoption.to_user_id}">{$adoption.to_username}</a></th>
				</tr>
			{/foreach}
		{else}
			<tr><td></td></tr>
		{/if}
		</table>
	{/if}

{else}
	<p class="errormsg">{$error}</p>
{/if}
