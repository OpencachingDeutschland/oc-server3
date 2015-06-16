{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-list.png" style="align: left; margin-right: 10px;" width="32" height="32" />
		{if $edit_list}{t}Edit cache list{/t}{else}{t}My cache lists{/t}{/if}
	</div>

	{if $invalid_waypoints}
		<p class="errormsg">{t}The following waypoints are invalid and could not be added to the list:{/t}&nbsp; {$invalid_waypoints}</p>
	{/if}

	{if !$edit_list}
		{include file="res_cachelists.tpl"}
		{if $cachelists|@count}
			<p><br />{t 1=$login.userid}Public lists are displayed in your <a href="viewprofile.php?userid=%1">public user profile</a>, on the <a href="cachelists.php">lists overwiew page</a> and in the cache listings.{/t}</p>
		{/if}
		<br />
	{/if}

	{literal}
	<script type="text/javascript">
	function newlist()
	{
	  document.getElementById('createnewlist').style.display='none';
		document.getElementById('addlist').style.display='block';
		document.getElementById('name_error').style.display='none';
		document.getElementById('list_name').value='';
		document.getElementById('list_caches').value='';
		document.getElementById('list_name').focus();
	}

	function cancel_newlist()
	{
		document.getElementById('addlist').style.display='none';
		document.getElementById('createnewlist').style.display='block';
	}
	</script>
	{/literal}

	<br />
	<form method="post" action="mylists.php">
		{if $edit_list}<input type="hidden" name="listid" value="{$listid}" />{/if}
		<span id="createnewlist" {if $name_error || $edit_list}style="display:none"{/if}>&nbsp;<input type="button" name="new" value="{t}Create new list{/t}" class="formbutton widebutton" onclick="newlist()" /></span>
		<table class="table" id="addlist" {if !($name_error || $edit_list)}style="display:none"{/if}>
			{if !$edit_list}
			<tr>
				<td colspan="2"><b>{t}Create new list{/t}:</b></td>
			</tr>
			<tr><td class="separator"></td></tr>
			{/if}
			<tr>
				<td>{t}Name{/t}:</td>
				<td><input type="text" id="list_name" name="list_name" maxlength="80" value="{$list_name}" class="input450" />
				<span id="name_error">{if $name_error}&nbsp;<span class="errormsg"><nobr>{t}Invalid name{/t}</nobr></span>{/if}</span></td>
			</tr>
			<tr>
				<td style="vertical-align:top">{t}Status{/t}:</td>
				<td><input type="radio" id="s_private" name="list_public" value="0" {if !$list_public}checked="checked"{/if} /><label for="s_private">{t}private{/t}</label> &nbsp; <input type="radio" id="s_public" name="list_public" value="1" {if $list_public}checked="checked"{/if} /><label for="s_public">{t}public{/t}</label><br />				
				</td>
			</tr>
			<tr>
				<td>{t}Watch{/t}:</td>
				<td><input type="checkbox" id="watch" name="watch" value="1" {if $watch}checked="checked"{/if} /> <label for="watch">{t}I want to receive notifications about any logs for caches in this list.{/t}</label></td>
			</tr>
			<tr><td class="separator"></td></tr>
			<tr>
				<td style="vertical-align:top; white-space:nowrap">{if $edit_list}{t}Add caches{/t}{else}{t}Caches{/t}{/if}:</td>
				<td><input type="text" id="list_caches" name="list_caches" maxlength="60" value="{$list_caches}" class="input450 waypoint" /><br /></td>
			</tr>
			<tr>
				<td></td>
				<td><img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Hint{/t}" align="middle" /> {t}Space-separated list of OC waypoints.{/t} {if $edit_list}{t}You may also add caches via "Add to list" button in cache listings.{/t}{else}{t}This is optional, caches may be added to the list later.{/t}{/if}</td>
			</tr>
			{if $edit_list && $caches|@count}
				<tr><td class="separator"></td></tr>
				<tr>
					<td style="vertical-align:top; padding-top:0.6em">{t}Remove caches{/t}:</td>
					<td>
						<table class="narrowtable">
						{foreach from=$caches item=cache}
							<tr>
								<td style="margin-left:0; padding-left:0; padding-right:3px"><input type="checkbox" name="remove_{$cache.cache_id}" value="1" /></td>
								<td><img src="resource2/{$opt.template.style}/images/cacheicon/16x16-{$cache.type}.gif" /></td>
								<td style="line-height:1.5em"><a href="viewcache.php?cacheid={$cache.cache_id}"><span style="{include file="res_cachestatus_span.tpl" status=$cache.status}">{if $cache.visible}{$cache.name|escape}{else}<i>{t}locked/hidden cache{/t}</i>{/if}</span></a> {include file="res_oconly.tpl" oconly=$cache.oconly}</td>
							</tr>
						{/foreach}
						</table>
					</td>
				</tr>
			{/if}
			<tr>
				<td colspan="2"><br />
				{if $edit_list}
					<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp; <input type="submit" name="save" value="{t}Save{/t}" class="formbutton" onclick="submitbutton('save')" />
				{else}
					<input type="button" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="cancel_newlist()" />&nbsp;&nbsp; <input type="submit" name="create" value="{t}Create list{/t}" class="formbutton" onclick="submitbutton('create')" />
				{/if}
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</form>

	{if $name_error}
	<script type="text/javascript">
		document.getElementById('list_name').focus();
	</script>
	{/if}
