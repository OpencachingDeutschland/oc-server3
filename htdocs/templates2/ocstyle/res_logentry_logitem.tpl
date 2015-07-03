{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<div id="log{$logItem.id}" style="clear:both">
<div class="content-txtbox-noshade">  {* Ocprop: <div class="content-txtbox-noshade">(.*?)<\/div> *}
	<div class="logs">
	<p class="content-title-noshade-size1 {if $print}printlogheader{/if}" style="display:inline; margin-right:0">
		{if $logItem.oc_team_comment}<img src="resource2/{$opt.template.style}/images/oclogo/oc-team-comment.png" alt="OC-Team" title="{t}OC team comment{/t}" />{/if}
		<a href="viewcache.php?cacheid={$cache.cacheid}&log=A#log{$logItem.id|urlencode}">{include file="res_logtype.tpl" type=$logItem.type}</a>  
		{if $logItem.recommended==1}  {* Ocprop: rating-star\.gif *}
			<img src="images/rating-star.gif" border="0" alt="{t}Recommended{/t}" width="17px" height="16px" />
		{/if}
		{$logItem.date|date_format:$opt.format.datelong}{if $logItem.time!="00:00:00"}, {$logItem.time|substr:0:5}{/if}

		{capture name=username}
			<a class="boldlink" href="viewprofile.php?userid={$logItem.userid}">{$logItem.username|escape}</a>
		{/capture}

		{if $logItem.type==1}  {* Ocprop: $htmluserid<\/a>\s*(hat das Event besucht|hat den Geocache gefunden|found the Geocache|has visited the event) *}
			{t 1=$smarty.capture.username}%1 found the geocache{/t}
		{elseif $logItem.type==2}  {* Ocprop: $htmluserid<\/a>.\s*(hat den Geocache nicht gefunden|didn't find the Geocache|didn't find the Geoacache) *}
			{t 1=$smarty.capture.username}%1 didn't find the geoacache{/t}
		{elseif $logItem.type==3}
			{t 1=$smarty.capture.username}%1 wrote a note{/t}
		{elseif $logItem.type==7}
			{t 1=$smarty.capture.username}%1 has visited the event{/t}
		{elseif $logItem.type==8}
			{t 1=$smarty.capture.username}%1 wants to visit the event{/t}
		{elseif $logItem.type==9}
			{t 1=$smarty.capture.username}%1 has archived the geocache{/t}
		{elseif $logItem.type==10}
			{if $logItem.oc_team_comment}
				{t 1=$smarty.capture.username}%1 has activated the geocache{/t}
			{else}
				{t 1=$smarty.capture.username}%1 has maintained the geocache{/t}
			{/if}
		{elseif $logItem.type==11}
			{t 1=$smarty.capture.username}%1 has disabled the geocache{/t}
		{elseif $logItem.type==13}
			{t 1=$smarty.capture.username}%1 has locked the geocache{/t}
		{elseif $logItem.type==14}
			{t 1=$smarty.capture.username}%1 has locked and hidden the geocache{/t}
		{else}
			{t 1=$smarty.capture.username}%1{/t}
		{/if}
	</p>

	{* Ocprop: /\?logid=([0-9a-f\-]+)" *}
	{if $logItem.deleted !== "1" && !$print && ($cache.userid==$login.userid || $logItem.userid==$login.userid)}
		<p class="editlog"><img src="images/trans.gif" border="0" width="16" height="16" alt="" title="" />
			{if $logItem.userid==$login.userid && ($cache.userid==$login.userid || $cache.status!=6 || $cache.adminlog)}
				<a href="editlog.php?logid={$logItem.id|urlencode}"><img src="resource2/{$opt.template.style}/images/action/16x16-properties.png" border="0" align="middle" border="0" width="16" height="16" alt="" /></a>
				[<a href="editlog.php?logid={$logItem.id|urlencode}">{t}Edit{/t}</a>]
			{/if}

			{if $cache.userid==$login.userid || $logItem.userid==$login.userid}
				<a href="removelog.php?logid={$logItem.id|urlencode}"><img src="resource2/{$opt.template.style}/images/action/16x16-delete.png" border="0" align="middle" border="0" width="16" height="16" alt="" /></a>
				[<a href="removelog.php?logid={$logItem.id|urlencode}">{t}Delete{/t}</a>]
			{/if}

			{if $logItem.userid==$login.userid && $cache.status!=6}
				<a href="picture.php?action=add&loguuid={$logItem.uuid|urlencode}"><img src="resource2/{$opt.template.style}/images/action/16x16-addimage.png" border="0" align="middle" border="0" width="16" height="16" alt="" /></a>
				[<a href="picture.php?action=add&loguuid={$logItem.uuid|urlencode}">{t}Upload picture{/t}</a>]
			{/if}
		</p>
	{/if}

	<div class="viewcache_log-content" style="margin-top: 15px;">
		{if $logItem.texthtml}
			<p>{$logItem.text}</p>
		{else}
			<p>{$logItem.text|smiley|hyperlink}</p>
		{/if}

		{foreach from=$logItem.pictures item=pictureItem name=pictures}
			{if $smarty.foreach.pictures.first}
				<b>{t}Pictures for this logentry:{/t}</b><br />
			{/if}

			{* the position of the following image is the anchor for enlargit activity: *}
			<a name="piclink" href="{$pictureItem.url}" onclick="enlarge(document.getElementById('pic{$pictureItem.id}'))" style="cursor:pointer">{$pictureItem.title|escape}<img id="pic{$pictureItem.id}" class="enlargegroup{$logItem.id}" src="resource2/ocstyle/images/misc/empty.png" longdesc="{$pictureItem.url}" title="{$pictureItem.title|replace:"'":"´"|replace:'"':'´´'}" alt="{$pictureItem.title|replace:"'":"´"|replace:'"':'´´'}" /></a> {* ' in title would cause enlargit and IE errors, even if escaped *}
			{if $pictureItem.spoiler}
				({t}Spoiler{/t})
			{/if}
			{if $logItem.userid==$login.userid}
				&nbsp;
				[<a href="picture.php?action=edit&uuid={$pictureItem.uuid|escape}">{t}Edit{/t}</a>]
				[<a href="javascript:if(confirm('{t escape=js}Do you really want to delete this picture?{/t}'))location.href='picture.php?action=delete&uuid={$pictureItem.uuid|escape}'">{t}Delete{/t}</a>]
			{/if}
			<br />
		{/foreach}

		{if $logItem.deleted_by_name != ""}
			<span style="color:red">{t}Deleted by{/t} {$logItem.deleted_by_name},
			{$logItem.deletion_date|date_format:$opt.format.date}</span>
		{/if}

	</div>
	<div style="clear:both"></div>
	</div>
</div>
</div>

