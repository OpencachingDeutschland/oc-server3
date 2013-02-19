{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{if $action=='edit'}
	<script type="text/javascript">
		{literal}
		<!--
		function intervalChanged()
		{
			var interval = document.getElementById('interval');
			var hour = document.getElementById('hour');
			var weekday = document.getElementById('weekday');

			switch (interval.selectedIndex)
			{
				case 0: // sofort
					hour.options[0].selected = true;
					weekday.options[0].selected = true;
					weekday.disabled=true;
					hour.disabled=true;
					break;
				case 1:	// taeglich
					weekday.disabled=true;
					hour.disabled=false;
					break;
				case 2: // woechentlich
					weekday.disabled=false;
					hour.disabled=false;
					break;
			}
		}
		//-->
		{/literal}
	</script>
	<form action="mywatches.php" method="post">
		<input type="hidden" name="action" value="edit" />

		<div class="content2-pagetitle">
			<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="Watched Caches" />
			{t}Watched Geocaches - Settings{/t}
		</div>

		<table class="table">
			<tr>
				<td>{t}Delivery:{/t}</td>
				<td>
					<select id="interval" name="interval" onChange="intervalChanged();" class="input100">
						<option value="0" {if $interval==0}selected="selected"{/if}>{t}Immediate{/t}</option>
						<option value="1" {if $interval==1}selected="selected"{/if}>{t}Daily{/t}</option>
						<option value="2" {if $interval==2}selected="selected"{/if}>{t}Weekly{/t}</option>
					</select>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>{t}Send at:{/t}</td>
				<td>
					<select id="hour" name="hour">
						{foreach from=$hours item=hourItem}
							<option value="{$hourItem.value}" {if $hourItem.value==$hour}selected="selected"{/if}>{$hourItem.time|date_format:$opt.format.time}</option>
						{/foreach}
					</select> {t}#timetitle{/t}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td>{t}Sent day:{/t}</td>
				<td>
					<select id="weekday" name="weekday" class="input100">
						{foreach from=$weekdays item=weekdayItem}
							<option value="{$weekdayItem.value}" {if $weekdayItem.value==$weekday}selected="selected"{/if}>{$weekdayItem.time|date_format:"%A"}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td class="help"><img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" /> {t}Sent time and day is only used with daily or weekly delivery.{/t}</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
			{if $error==true}
				<tr><td colspan="2" class="errormsg">{t}Error while trying to save!{/t}</td></tr>
			{elseif $saved==true}
				<tr><td colspan="2" class="successmsg">{t}Settings stored successfull.{/t}</td></tr>
			{/if}
			<tr><td class="spacer" colspan="2"></td></tr>

			<tr>
				<td class="header-small" colspan="2">
					<input type="reset" name="cancel" value="{t}Reset{/t}" class="formbuttons" />&nbsp;&nbsp;
					<input type="submit" name="ok" value="{t}Change{/t}" class="formbuttons" />
				</td>
			</tr>
		</table>
	</form>

	<script type="text/javascript">
		{literal}
		<!--
			intervalChanged();
		//-->
		{/literal}
	</script>

{else}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-searchresults.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="Watched Caches" />
		{t}Watched Geocaches{/t}
	</div>

	<table class="null" border="0" cellspacing="0" width="98%">
		<tr>
			<td colspan="2">
				<table class="table">
					<tr class="searchresult">
						<td width="50px"><b>{t}Type{/t}</b></td>
						<td width="50px"><b>{t}State{/t}</b></td>
						<td width="500px"><b>{t}Name{/t}</b></td>
						<td width="100px"><b>{t}Last found{/t}</b></td>
						<td width="100px">&nbsp;</td>
					</tr>
					{foreach from=$watches item=watchItem}
						{cycle values="#eeeeee,#e0e0e0" assign=bgcolor}
						<tr>
							<td style="border-bottom: solid 1px grey;">{include file="res_cacheicon_22.tpl" cachetype=$watchItem.type|escape}</td>
							<td style="border-bottom: solid 1px grey;">{include file="res_cachestatus.tpl" status=$watchItem.status}</td>
							<td style="border-bottom: solid 1px grey;"><span style="{include file="res_cachestatus_span.tpl" status=$ratingItem.status}"><a href="viewcache.php?wp={$watchItem.wp}">{$watchItem.name|escape}</a></span></td>
							<td style="border-bottom: solid 1px grey;">
								{if $watchItem.lastfound==null}
									---
								{else}
									{$watchItem.lastfound|date_format:$opt.format.date}
								{/if}
							</td>
							<td style="border-bottom: solid 1px grey;">[<a href="javascript:if(confirm('{t escape=js}Do you really want to delete this entry?{/t}'))location.href='mywatches.php?action=remove&cacheid={$watchItem.cacheid}&target=mywatches.php'">{t}remove{/t}</a>]</td>
						</tr>
					{foreachelse}
						<tr><td colspan="5">{t}No Geocaches watched.{/t}</td></tr>
					{/foreach}
				</table>
			</td>
		</tr>
	</table>
{/if}
