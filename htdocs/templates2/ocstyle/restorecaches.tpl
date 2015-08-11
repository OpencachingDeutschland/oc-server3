{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="margin-right: 10px;" width="32" height="32" alt="World" />
	{t}Revert Vandalism{/t}
</div>

<div class="content2-container">
{* step 1: select user *}
{if $step == 1}
	<form method="post" action="restorecaches.php">
		<input type="hidden" name="finduser" value="1" />
		<br />
		<p><strong>{t}Username{/t}:</strong>
			&nbsp;<input type="text" name="username" size="30" /></p>
		<br />
		<p><input type="submit" name="find" value="{t}Submit{/t}" class="formbutton" onclick="javascript:submitbutton('find')" /></p>
	</form>

	{if $error != ""}
		<br />
		<p class="errormsg">
		{if $error=='userunknown'}
			{t 1=$username}User '%1' is unknown{/t}
		{elseif $error == "nocaches"}
			{t 1=$username}%1 has not listed any caches{/t}
		{/if}
		</p>
	{/if}

{elseif $step > 1}

	<h2>{t 1=$username}Restore cache listings of %1{/t}</h2>

	{if !$disabled && $step<5}
		<p class="redtext">{t 1=$username}User '%1' is not disabled. You can view recorded changes, but not revert them.{/t}</p>
	{/if}

	{if $error != "" && ($error  != "notsure" || !$simulate)}
		<br />
		<p class="errormsg">
		{if $error == "nocaches"}
			{t}You did not select any caches.{/t}
		{elseif $error == "nodata"}
			{t}No saved data is available for these caches.{/t}
		{elseif $error == "nodate"}
			{t}You must select a date.{/t} {t}Use your browser's 'back' button to try again.{/t}
		{elseif $error == "nochecks"}
			{t}You must choose the listing elements to be restored.{/t} {t}Use your browser's 'back' button to try again.{/t}
		{elseif $error == "notsure"}
			{t}You did not say that you are sure.{/t} {t}Use your browser's 'back' button to try again.{/t}
		{/if}
		</p>
		{if $error!="notsure" && $error!="nodate" && $error!="nochecks"}
			<br />
			<form method="post" action="restorecaches.php">
				<input type="hidden" name="finduser" value="1" />
				<input type="hidden" name="username" value="{$username}" />
				<input type="submit" class="formbutton" value="{t}Back{/t}" onclick="submitbutton('submit')" />
			</form>
		{/if}

	{* step 3: select caches *}
	{elseif $step == 3}
		{if $disabled}
			<p>{t}Please select the listings to restore{/t}:</p>
		{/if}
		<br />
		<form method="post" action="restorecaches.php">
			<input type="hidden" name="caches" value="1" />
			<input type="hidden" name="username" value="{$username}" />
			<table class="table">
				<tr>
					<th></th>
					<th style="text-align:right">{t}Waypoint{/t}</th>
					<th>{t}Coordinates{/t}</th>
					<th>{t}Logs{/t}</th>
					<th>{t}modified{/t}</th>
					<th>{t}Cachename{/t}</th>
				</tr>
				{foreach from=$aCaches item=cache}
					<tr>
						<td>{if $cache.data|@count > 0}<input type="checkbox" name="cache_{$cache.cache_id}" value="1" />{/if}</td>
						<td style="white-space:nowrap">{include file="res_cachestatus.tpl" status=$cache.status}&nbsp;<a href="viewcache.php?cacheid={$cache.cache_id}" target="_ocv">{$cache.wp_oc}</a></td>
						<td style="font-size:1em; line-height:1em; white-space:nowrap">{$cache.coordinates.lat|escape}<br />{$cache.coordinates.lon|escape}</td>
						<td style="text-align:right">{$cache.logs}&nbsp;</td>
						<td>{$cache.last_modified|date_format:$opt.format.date}</td>
						<!-- <td>{if $cache.data|@count > 0}{$cache.date|date_format:$opt.format.date}&nbsp;/{$cache.data|@count}{/if}</td> -->
						<td style="line-height:1.1em">{$cache.name}</td>
					</tr>
				{/foreach}
				<tr><td style="height:4px"></td></tr>
			</table>

			<p><input type="submit" class="formbutton" name="tostep4" value="{t}Go on{/t}" onclick="submitbutton('tostep4')" /> &nbsp;{t}to the date selection{/t}</p>
		</form>

	{* step 4: select date *}
	{elseif $step == 4}
		{literal}
		<script type="text/javascript">
			function checkall(value)
			{
				document.getElementsByName("restore_coords")[0].checked = value;
				document.getElementsByName("restore_settings")[0].checked = value;
				document.getElementsByName("restore_waypoints")[0].checked = value;
				document.getElementsByName("restore_desc")[0].checked = value;
				document.getElementsByName("restore_logs")[0].checked = value;
			}
		</script>
		{/literal}

		<form method="get" action="restorecaches.php">
			<input type="hidden" name="username" value="{$username}" />
			<input type="hidden" name="cacheids" value="{$cachelist}" />
			<input type="hidden" name="doit" value="1" />
			{if $today}
				<p class="redtext">{t}The user changed one or more of these caches today, therefore you cannot revert changes. This can be done not before tomorrow.{/t}</p>
			{/if}
			{if ($disabled && !$today) || $rootadmin}
				<p>{t}Please select the date from which on all changes are to be reverted.<br />The listing will be reset to the contents it had on that day at 00:00:00.{/t}</p>
			{/if}
			<br />
			<table class="narrowtable">
				<tr>
					<th></th>
					<th>{t}Date{/t}</th>
					<th>{t}Waypoint{/t}</th>
					<th>{t}Changes{/t}</th>
				</tr>

				{foreach from=$dates key=date item=caches}
					<tr>
						<td>{if ($disabled && !$today) || $rootadmin}<input type="radio" name="dateselect" value="{$date}" />{/if}</td>
						<td>{$date|date_format:$opt.format.date}</td>
					</tr>
			 		{foreach from=$caches key=wp item=text}
			 			<tr>
			 				<td></td>
			 				<td></td>
				 			<td style="vertical-align:top"><a href="viewcache.php?wp={$wp}" target="_ocv">{$wp}</a></td>
				 			<td>{$text}</td>
			 			</tr>
		 			{/foreach}
	 				<tr>
					 	<td colspan="4"><hr /></td>
					 </tr>
				{/foreach}
			</table>

			<br />
			{if $today && $rootadmin}
				<p class="redtext">{t}Warning: If you revert any owner-made listing changes of <em>today</em>, your revert will be final. It cannot be corrected / undone afterwards. Only reverts of coords &amp; country, logs and pictures will be logged in this case, so all other changes will not be comprehensible. Therefore <span style="text-decoration:underline">it is strongly recommended to revert vandalism not before the next day!</span>{/t}</p>
			{/if}
			{if $disabled || $rootadmin}
				<p>{t}Restore{/t} ...</p>
				<p>
					<input type="checkbox" name="restore_coords" value="1" /> {t}coordinates and country{/t}&nbsp;&nbsp;
					<input type="checkbox" name="restore_settings" value="1" /> {t}name, settings, attributes and hide-date{/t} &nbsp;&nbsp;
					<input type="checkbox" name="restore_waypoints" value="1" /> {t}GC/NC waypoints{/t} <br />
					<input type="checkbox" name="restore_desc" value="1" /> {t}description(s) incl. pictures{/t} &nbsp;&nbsp;
					<input type="checkbox" name="restore_logs" value="1" /> {t}logs incl. pictures{/t} &nbsp;&nbsp;
					<a href="javascript:checkall('checked')">{t}_all{/t}</a> &nbsp; <a href="javascript:checkall('')">{t}nothing{/t}</a>
				</p>
				<p>
					<em>{t}Excluded from restore: cache status, OConly attribute, additional waypoints, log password, preview picture status{/t}</em>
				</p>
				<p>
					<input type="checkbox" name="sure" value="1" /> {t}Sure?{/t} &nbsp;&nbsp;
			  	<input type="checkbox" name="simulate" value="1" /> {t}simulate{/t}
				</p>
				<br />
				<p><input type="submit" class="formbutton" name="revert" value="{t}Revert Vandalism{/t}" style="width:200px" onclick="submitbutton('revert')" /></p>
				{if !$disabled && $rootadmin}
					<p>{t}You are root admin and can override the warnings. Take care!{/t}</p>
				{/if}
			{/if}
		</form>

	{* step 4: listings are restored - show result *}
	{elseif $step == 5}
		<br />
		<p>
			{if $simulate}
				{t 1=$date|date_format:$opt.format.date}The following cache listings would have been reset to the state before %1{/t}:
			{else}
				{t 1=$date|date_format:$opt.format.date}The following cache listings have been reset to the state before %1{/t}:
			{/if}
		</p>

		{if $restored|@count == 0}
			<p>({t}none{/t})</p>
		{else}
			<ul>
				{foreach from=$restored key=wp item=fields}
					<li>
						<a href="viewcache.php?wp={$wp}" target="_ocv">{$wp}</a>:
						{assign var=first value=0}
						{foreach from=$fields key=field item=dummy}{if $first > 0}, {/if}{$field}{assign var=first value=1}{/foreach}
					</li>
				{/foreach}
			</ul>
		{/if}
	{/if}

{/if}
</div>
