{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}

{* JS for cache list description tooltips *}
<script type="text/javascript" src="resource2/{$opt.template.style}/js/wz_tooltip.js"></script>

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-list.png" style="margin-right: 10px;" width="32" height="32" />
		{if $newlist_mode}{t}Create new cachelist{/t}{elseif $edit_list}{t}Edit cache list{/t}{else}{t}My cache lists{/t}{/if}
	</div>

	{if $invalid_waypoints}
		<p class="errormsg">{t}The following waypoints are invalid and could not be added to the list:{/t}&nbsp; {$invalid_waypoints}</p>
	{/if}

	{if !$edit_list && !$newlist_mode && !$name_error}
		{include file="res_cachelists.tpl" title_ownlists=true}
		{if $cachelists|@count}
			<p><br />{t 1=$login.userid}Public lists are displayed in your <a href="viewprofile.php?userid=%1">public user profile</a>, on the <a href="cachelists.php">lists overwiew page</a> and (if enabled) in the cache listings.{/t}</p>
		{/if}
		<br />
		<form method="post" action="mylists.php">
			&nbsp;<input type="submit" name="new" value="{t}Create new list{/t}" class="formbutton widebutton" />
		</form>
		<br />
	{else}

	{literal}
	<script type="text/javascript">
	function showdesc()
	{
		document.getElementById('desc0').style.display = 'none';
		document.getElementById('desc1').style.display = '';
		document.getElementById('desc2').style.display = '';
		document.getElementById('desc3').style.display = '';
		document.getElementById('desc3').style.width = '580px';
	}
	function state_changed()
	{
		var lkd =  document.getElementById("list_password_data");
		if (typeof(lkd) !== 'undefined' && lkd != null)
			lkd.style.display = (document.getElementById("s_private").checked ? "inline" : "none");
	}
	</script>
	{/literal}

	<form method="post" action="mylists.php?id={$listid}" name="editform" id="editlist_form">
		{if $edit_list}<input type="hidden" name="listid" value="{$listid}" />{/if}
		<input id="descMode" type="hidden" name="descMode" value="3" />
		<input type="hidden" id="switchDescMode" name="switchDescMode" value="0" />
		{if $fromsearch}<input type="hidden" name="fromsearch" value="{$fromsearch}" />{/if}

		<table class="table" id="addlist" {if !($name_error || $edit_list || $newlist_mode)}style="display:none"{/if}>
			<tr><td class="separator"></td></tr>
			<tr><td class="separator"></td></tr>
			<tr>
				<td>{t}Name:{/t}</td>
				<td><input type="text" id="list_name" name="list_name" maxlength="80" value="{$list_name}" class="input500" /></td>
			</tr>
			{if $name_error}
				<tr><td></td>
				<td><nobr><span id="name_error">{if $name_error}&nbsp;<span class="errormsg">{if $name_error==ERROR_DUPLICATE_LISTNAME}{t}Another of your cache lists already has this name.{/t}{else}{if $name_error==ERROR_BAD_LISTNAME}{t}Invalid name{/t}{if $list_visibility>=2}; {t}minimum length for public lists is 10 characters{/t}{/if}{/if}{/if}</span></nobr>{/if}</span></td>
				</tr>
			{/if}
			<tr><td class="separator"></td></tr>
			<tr>
				<td style="vertical-align:top">{t}State:{/t}</td>
				<td><input type="radio" class="radio" id="s_private" name="list_visibility" value="0" {if $list_visibility==0}checked="checked"{/if} onchange="state_changed()" onfocus="state_changed()" {* old MSIE versions need onfocus(), onchange() wont work properly *} /><label for="s_private">{t}private{/t}</label><span id="list_password_data" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {t}Password for sharing:{/t} &nbsp;<input type="text" id="list_password" name="list_password" maxlength="80" value="{$list_password}" class="input200" /></span><br /> 
				{* visibility 2 is reserve for friends *}<input type="radio" class="radio" id="s_public" name="list_visibility" value="2" {if $list_visibility==2}checked="checked"{/if} onchange="state_changed()" onfocus="state_changed()"  /><label for="s_public">{t}public{/t}</label><br />
				<input type="radio" class="radio" id="s_public3" name="list_visibility" value="3" {if $list_visibility==3}checked="checked"{/if} onchange="state_changed()" onfocus="state_changed()" /><label for="s_public3" >{t}public{/t} + {t}visible for all users in cache listings{/t}</label><br />				
				</td>
			</tr>
			<tr><td class="separator"></td></tr>
			<tr>
				<td>{t}Watch:{/t}</td>
				<td><input type="checkbox" class="checkbox" id="watch" name="watch" value="1" {if $watch}checked="checked"{/if} /> <label for="watch">{t}I want to receive notifications about any logs for caches in this list.{/t}</label></td>
			</tr>
			<tr><td class="separator"></td></tr>

			<tr id="desc1" style="{if $desctext=='' && !$show_editor}display:none{/if}" >
				<td></td>
				<td><span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter (HTML) text only. To use the editor, please enable JavaScript.{/t}</span></td>
			</tr>
			<tr>
				<td style="vertical-align:top; padding-top:0.2em;">
					{t}Description:{/t}<br />
					<div id="desc2" class="menuBar" style="margin-top:12px; {if $desctext=='' && !$show_editor}display:none{/if}" >
						<nobr>
						<span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">{t}Editor{/t}</span>
						<span class="buttonSplitter">|</span>
						<span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">{t}&lt;html&gt;{/t}</span>
						</nobr>
					</div>
				</td>
				<td id="desc0" style="{if $desctext!='' || $show_editor}display:none{/if}" >
					<input type="button" value="{t}Add{/t}" onclick="javascript:showdesc()" class="formbutton" />
				</td>
				<td id="desc3" style="{if $desctext=='' && !$show_editor}display:none{/if}" >
					<textarea name="desctext" id="desctext" cols="70" rows="7" class="listdesc{$descMode}" >{$desctext}</textarea>
					{if $descMode==2}<br />{/if}
					<span class="smalltext">{t}By submitting I accept the <a href="articles.php?page=impressum#tos" target="_blank">Opencaching.de Terms of Service</a> and the <a href="articles.php?page=impressum#datalicense" target="_blank">Opencaching.de Datalicense</a>{/t}</span>
				</td>
			</tr>
			<tr><td class="separator"></td></tr>
			<tr><td class="separator"></td></tr>
			<tr>
				<td style="vertical-align:top; white-space:nowrap">{if $edit_list}{t}Add caches:{/t}{else}{t}Caches:{/t}{/if}</td>
				<td><input type="text" id="list_caches" name="list_caches" maxlength="1000" value="{$list_caches}" class="input500 waypoint" /><br /></td>
			</tr>
			<tr>
				<td></td>
				<td><img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Hint{/t}" align="middle" /> {t}Space-separated list of OC waypoints.{/t} {if $edit_list}{t}You may also add caches via "Add to list" button in cache listings.{/t}{else}{t}This is optional, caches may be added to the list later.{/t}{/if}</td>
			</tr>
			{if $edit_list && $caches|@count}
				<tr><td class="separator"></td></tr>
				<tr>
					<td style="vertical-align:top; padding-top:0.6em">{t}Remove caches:{/t}</td>
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
				<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp; 
				{if $newlist_mode}
					<input type="submit" name="create" value="{t}Create list{/t}" class="formbutton" onclick="submitbutton('create')" />
				{else}
					<input type="submit" name="save" value="{t}Save{/t}" class="formbutton" onclick="submitbutton('save')" />
				{/if}
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</form>

	<script type="text/javascript">
	OcInitEditor();
	{if $name_error || $newlist_mode}
		document.getElementById('list_name').focus();
	{/if}
	{* Initially hide password input field for non-private setting. Doing it this way
	   ensures that it is visible if Javascript is not available: *}
	state_changed();
	</script>

	{/if}   {* edit_list *}

	{if $bookmarked_lists|@count && !$edit_list && !$newlist_mode && !$name_error}
		<br id="bookmarks" />
		{include file="res_cachelists.tpl" cachelists=$bookmarked_lists title_bookmarks=true
		 show_bookmarks=true show_user=tre show_watchers=false show_edit=false show_unbookmark=true}
	{/if}
