{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="margin-right: 10px;" width="32" height="32" alt="" />
	{if $ownerlogs}
		{if $ownlogs}
			{t}Log entries for your geocaches{/t}
		{else}
			{capture name=ownerlink}<a href="viewprofile.php?userid={$ownerid}">{$ownername|escape}</a>{/capture}
			{t 1=$smarty.capture.ownerlink}Newest log entries for caches of %1{/t}
		{/if}
	{elseif $ownlogs}
		{t}Your log entries{/t}
	{else}
		{t}Latest logs entries{/t} {if $rest}{t}Without Germany{/t}{/if}
	{/if}
</div>

<p style="line-height:2em">
	{if $paging}
		{include file="res_pager.tpl"}
		&nbsp; &nbsp;
	{/if}
	{if $ownerlogs && $ownlogs}
		{if $show_own_logs}
			<a class="systemlink" href="ownerlogs.php?ownlogs=0">{t}Hide own logs{/t}</a>
		{else}
			<a class="systemlink" href="ownerlogs.php?ownlogs=1">{t}Show own logs{/t}</a>
		{/if}
	{/if}
</p>

<table width="100%" class="table"> 
	{assign var='lastCountry' value=''}

	{foreach name=newLogs from=$newLogs item=newLog}
		{if $newLogsPerCountry}
			{if $newLog.country_name!=$lastCountry}
				<tr><td class="spacer"></td></tr>
				<tr><td colspan="3">
					<table cellspacing="0" cellpadding="0"><tr>
						<td class="content-title-flag" ><img src="images/flags/{$newLog.country|lower}.gif" /></td>
						<td><b class="content-title-noshade-size08">{$newLog.country_name|escape}</b>&nbsp;</b></td>
					</tr></table>
				</td></tr>
			{/if}
		{/if}
		<tr>
			<td style="width:1px">
				{if $creation_date}{$newLog.date_created|date_format:$opt.format.date}{else}{$newLog.date|date_format:$opt.format.date}{/if}
			</td>
			<td class="listicon">
				{if $newLog.type==1}
					<img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==2}
					<img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==3}
					<img src="resource2/{$opt.template.style}/images/log/16x16-note.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==7}
					<img src="resource2/{$opt.template.style}/images/log/16x16-attended.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==8}
					<img src="resource2/{$opt.template.style}/images/log/16x16-will_attend.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==9}
					<img src="resource2/{$opt.template.style}/images/log/16x16-archived.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==10}
					<img src="resource2/{$opt.template.style}/images/log/16x16-active.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==11}
					<img src="resource2/{$opt.template.style}/images/log/16x16-disabled.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==13}
					<img src="resource2/{$opt.template.style}/images/log/16x16-locked.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{elseif $newLog.type==14}
					<img src="resource2/{$opt.template.style}/images/log/16x16-locked-invisible.png" width="16" height="16" border="0" alt="" style="margin-top:4px;" /> 
				{/if}
			</td>
			<td>
				{if $newLog.oc_team_comment}<img src="resource2/{$opt.template.style}/images/oclogo/oc-team-comment.png" alt="OC-Team" title="{t}OC team comment{/t}" />{/if}
				{capture name=cachename}
					<a href="viewcache.php?wp={$newLog.wp_oc}&log=A#log{$newLog.id}">{if $newLog.first}<b>{$newLog.cachename|escape}</b>{else}{$newLog.cachename|escape}{/if}</a>
				{/capture}
				{capture name=username}
					<a href="viewprofile.php?userid={$newLog.user_id}">{$newLog.username|escape}</a>
				{/capture}

				{if $newLog.type==1}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 found %1{/t}
					{if $newLog.recommended}<img src="images/rating-star.gif" width="17" height="16" title="{t}with recommendation{/t}" />{/if}
				{elseif $newLog.type==2}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 didn't find %1{/t}
				{elseif $newLog.type==3}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 wrote a note for %1{/t}
				{elseif $newLog.type==7}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 visited %1{/t}
					{if $newLog.recommended}<img src="images/rating-star.gif" width="17" height="16" title="{t}with recommendation{/t}" />{/if}
				{elseif $newLog.type==8}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 want's to visit %1{/t}
				{elseif $newLog.type==9}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has archived %1{/t}
				{elseif $newLog.type==10}
					{if $newLog.oc_team_comment}
						{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has activated %1{/t}
					{else}
						{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has maintainted %1{/t}
					{/if}
				{elseif $newLog.type==11}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has disabled %1{/t}
				{elseif $newLog.type==13 || $newLog.type==14}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 has locked %1{/t}
				{/if}

				{if $newLog.pics}
					<img src="resource2/ocstyle/images/action/16x16-addimage.png" />
					{if $newLog.picshown}&rarr;{/if}
				{/if}
			</td>
			{if $newLog.pic_uuid != ""}
				<td rowspan="{$lines_per_pic}">
					{include file="res_logpicture.tpl" picture=$newLog logdate=false loguser=false nopicshadow=true}
				</td>
				<td></td>
			{/if}
		</tr>
		{assign var='lastCountry' value=$newLog.country_name}
	{foreachelse}
		{if $ownerlogs}
			<p>{t}There are no log entries yet for your geocaches.{/t}</p>
		{/if}
	{/foreach}
	<tr><td class="spacer" style="height:{$addpiclines}em"></td></tr>
</table>

{if $paging && $newLogs|@count > 20}
	<p>
		{include file="res_pager.tpl"}
	</p>
	<br />
{/if}
