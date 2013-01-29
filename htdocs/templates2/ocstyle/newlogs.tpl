{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/cacheicon/traditional.gif" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Latest logs entries{/t}" />
	{t}Latest logs entries{/t}
</div>

<table width="100%" class="table">
	{assign var='lastCountry' value=''}

	{foreach name=newLogs from=$newLogs item=newLog}
		{if $newLogsPerCountry}
			{if $newLog.country_name!=$lastCountry}
				<tr><td class="spacer"></td></tr>
				<tr><td><b>{$newLog.country_name|escape}</b></td><tr>
			{/if}
		{/if}
		<tr>
			<td>
				{$newLog.date_created|date_format:$opt.format.date} - 
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
				{/if}

				{capture name=cachename}
					<a href="viewcache.php?wp={$newLog.wp_oc}">{$newLog.cachename|escape}</a>
				{/capture}
				{capture name=username}
					<a href="viewprofile.php?userid={$newLog.user_id}">{$newLog.username|escape}</a>
				{/capture}

				{if $newLog.type==1}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 found %1{/t}
				{elseif $newLog.type==2}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 didn't find %1{/t}
				{elseif $newLog.type==3}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 wrote a note for %1{/t}
				{elseif $newLog.type==7}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 visited %1{/t}
				{elseif $newLog.type==8}
					{t 1=$smarty.capture.cachename 2=$smarty.capture.username}%2 want's to visit %1{/t}
				{/if}

			</td>
		</tr>
		{assign var='lastCountry' value=$newLog.country_name}
	{/foreach}
</table>