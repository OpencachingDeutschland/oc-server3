{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
{strip}
<form method="POST" action="adminreports.php">
	<input type="hidden" name="rid" value="{$id}" />
	<input type="hidden" name="cacheid" value="{$cacheid}" />
	<input type="hidden" name="ownerid" value="{$ownerid}" />

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="World" />
		{t}Reported caches{/t}
	</div>

	{if $error == 1}
		<p style="line-height: 1.6em;">{t}The report is already assigned to another admin!{/t}</p>
	{elseif $error == 2}
		<p style="line-height: 1.6em;">{t}The report is already assigned to you!{/t}</p>
	{elseif $error == 3}
		<p style="line-height: 1.6em;">{t}You can not work on this report! Another admin is already pursuing it.{/t}</p>
	{elseif $error == 4}
		<p style="line-height: 1.6em;">{t}To work on a report you have to assign it to you!{/t}</p>
	{/if}

	{if $list == true}
		<ul>
			{foreach from=$reportedcaches item=rc}
				<li><a href="adminreports.php?id={$rc.id}">{$rc.new|escape}{$rc.name|escape} reported by {$rc.username|escape} ({$rc.lastmodified|date_format:$opt.format.datelong})</a></li>
			{foreachelse}
				<li>{t}No reported caches{/t}</li>
			{/foreach}
		</ul>
		{if $reportedcaches != NULL}
			<p style="line-height: 1.6em;">{t}(*) New reports{/t}</p>
		{/if}
	{else}
		<p style="line-height: 1.6em;">{t}Details for report of {/t} <a href="viewcache.php?cacheid={$cacheid}" target="_blank">{$cachename|escape}</a> {t} by {/t} <a href="viewprofile.php?userid={$userid}" target="_blank">{$usernick|escape}</a></p>
		<p style="line-height: 1.6em;"><b>{t}Last modified:{/t}</b>&nbsp;{$lastmodified|date_format:$opt.format.datelong}</p>
		<p style="line-height: 1.6em;"><b>{t}State:{/t}</b>&nbsp;{$status}&nbsp;<b>Admin:</b>&nbsp;{if $adminnick==''}{t}not assigned{/t}{else}{$adminnick|escape}{/if}</p>
		<p style="line-height: 1.6em;"><b>{t}Reason:{/t}</b>&nbsp;{$reason|escape|nl2br}</p>
		<p style="line-height: 1.6em;"><b>{t}Comment:{/t}</b>&nbsp;{$note|escape|nl2br}</p>

		<div class="content2-container bg-blue02">
  		<p class="content-title-noshade-size2">
				<img src="resource2/{$opt.template.style}/images/description/22x22-misc.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
	  		{t}Action{/t}
	  	</p>
	  </div>

		<p style="line-height: 1.6em;"><input type="submit" name="assign" value="{t}Assign to me{/t}" class="formbuttons" />&nbsp;&nbsp;<input type="submit" name="contact" value="{t}Contact owner{/t}" class="formbuttons" />&nbsp;&nbsp;<input type="submit" name="done" value="{t}Mark as finished{/t}" class="formbuttons" /></p>

		<div class="content2-container bg-blue02">
	  	<p class="content-title-noshade-size2">
	  		<img src="resource2/{$opt.template.style}/images/description/22x22-utility.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" /> 
	  		{t}Set state{/t}
	  	</p>
	  </div>

		<p style="line-height: 1.6em;"><input type="submit" name="statusActive" value="{t}Ready for search{/t}" class="formbuttons" />&nbsp;&nbsp;<input type="submit" name="statusTNA" value="{t}Temporary not available{/t}" class="formbuttons" />&nbsp;&nbsp;<input type="submit" name="statusArchived" value="{t}Archived{/t}" class="formbuttons" />&nbsp;&nbsp;<input type="submit" name="statusLockedVisible" value="{t}Locked, visible{/t}" class="formbuttons" />&nbsp;&nbsp;<input type="submit" name="statusLockedInvisible" value="{t}Locked, invisible{/t}" class="formbuttons" /></p>
	{/if}

</form>
{/strip}
