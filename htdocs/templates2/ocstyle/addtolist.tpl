{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<form method="post" action="addtolist.php">
	<input type="hidden" name="action" value="add" />
	<input type="hidden" name="submit" value="1" />
	<input type="hidden" name="cacheid" value="{$cacheid}" />

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/misc/32x32-list.png" style="margin-right: 10px;" width="32" height="32" alt="" />
		{t}Add geocache to list{/t}
	</div>

	<p>
		<br />
		{t 1=$cachename|escape}Add the geocache <b>%1</b> to the following list:{/t}<br />
		<br />
	</p>

	<p style="margin-left:16px">
		<input type="radio" class="radio" id="newlist" name="listid" value="0" {if $default_list==0}checked="checked"{/if} />
		<input type="text" name="newlist_name" maxlength="80" class="input400" value="{if $newlist_name !== false}{$newlist_name}{else}{t}New cache list{/t}{/if}" onfocus="if (this.value == '{t}New cache list{/t}') this.value=''; newlist.checked=1;" onblur="if (this.value == '') this.value = '{t}New cache list{/t}';" />
		&nbsp;
		<input type="checkbox" class="checkbox" id="newlist_public" name="newlist_public" value="1" {if $newlist_public}checked="checked"{/if} /> <label for="newlist_public">{t}public list{/t}</label>
		&nbsp;
		<input type="checkbox" class="checkbox" id="newlist_watch" name="newlist_watch" value="1" {if $newlist_watch}checked="checked"{/if} /> <label for="newlist_watch">{t}watch{/t}</label>
		{if $name_error}<br /><div style="margin-left:42px"><p class="errormsg">{if $name_error==ERROR_DUPLICATE_LISTNAME}{t}Another of your cache lists already has this name.{/t}{else}{if $name_error==ERROR_BAD_LISTNAME}{t}Invalid name{/t}{if $newlist_public}; {t}minimum length for public lists is 10 characters{/t}{/if}{/if}{/if}</p></div>{/if}
	</p>

	<p style="margin-left:16px">
		<span class="radiolist">
		{foreach from=$cachelists item=cachelist}
			<input type="radio" class="radio" id="list{$cachelist.id}" name="listid" value="{$cachelist.id}" {if $default_list==$cachelist.id}checked="checked"{/if} />
			<label for="list{$cachelist.id}">{$cachelist.name|escape}
			&nbsp;<span class="greytext">({if $cachelist.visibility<2}{t}private{/t}{else}{t}public{/t}{if $cachelist.visibility==3} + {t}listing display{/t}{/if}{/if})</span></label>
			<br />
		{/foreach}
		</span>
	</p>

	<p>
		<br />
		{t}You can maintain your personal cache lists in your <a href="mylists.php">user profile</a>.{/t} {t 1=$login.userid}Public lists are displayed in your <a href="viewprofile.php?userid=%1">public user profile</a>, on the <a href="cachelists.php">lists overwiew page</a> and (if enabled) in the cache listings.{/t}<br />
		<br />
	</p>

	<input type="submit" name="cancel" value="{t}Cancel{/t}" class="formbutton" onclick="submitbutton('cancel')" />&nbsp;&nbsp;
	<input type="submit" name="save" value="{t}Add{/t}" class="formbutton" onclick="submitbutton('add')" />

</form>
