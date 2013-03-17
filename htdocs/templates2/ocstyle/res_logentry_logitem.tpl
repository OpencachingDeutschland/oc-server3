{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<div class="content-txtbox-noshade" id="log{$logItem.id}">
	<div class="logs">
	<p class="content-title-noshade-size1" style="display:inline;">
		{include file="res_logtype.tpl" type=$logItem.type} 
		{if $logItem.recommended==1}
			<img src="images/rating-star.gif" border="0" alt="{t}Recommended{/t}" width="17px" height="16px" />
		{/if}
		{$logItem.date|date_format:$opt.format.datelong}

		{capture name=username}
			<a href="viewprofile.php?userid={$logItem.userid}">{$logItem.username|escape}</a>
		{/capture}

		{if $logItem.type==1}
			{t 1=$smarty.capture.username}%1 found the Geocache{/t}
		{elseif $logItem.type==2}
			{t 1=$smarty.capture.username}%1 didn't find the Geoacache{/t}
		{elseif $logItem.type==3}
			{t 1=$smarty.capture.username}%1 wrote a note{/t}
		{elseif $logItem.type==7}
			{t 1=$smarty.capture.username}%1 has visited the event{/t}
		{elseif $logItem.type==8}
			{t 1=$smarty.capture.username}%1 wants to visit the event{/t}
		{else}
			{t 1=$smarty.capture.username}%1{/t}
		{/if}
	</p>

	{if $cache.userid==$login.userid || $logItem.userid==$login.userid}
		<p style="font-weight: 400;display:inline;"><img src="images/trans.gif" border="0" width="16" height="16" alt="" title="" />
			{if $logItem.userid==$login.userid && ($cache.userid==$login.userid || $cache.status!=6)}
				<a href="editlog.php?logid={$logItem.id|urlencode}"><img src="resource2/{$opt.template.style}/images/action/16x16-properties.png" border="0" align="middle" border="0" width="16" height="16" alt="" /></a>
				[<a href="editlog.php?logid={$logItem.id|urlencode}">{t}Edit{/t}</a>]
			{/if}

			{if $cache.userid==$login.userid || $logItem.userid==$login.userid}
				<a href="removelog.php?logid={$logItem.id|urlencode}"><img src="resource2/{$opt.template.style}/images/log/16x16-trash.png" border="0" align="middle" border="0" width="16" height="16" alt="" /></a>
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
			<a name="piclink" href="{$pictureItem.url}" onclick="enlarge(document.getElementById('pic{$pictureItem.id}'))" style="cursor:pointer">{$pictureItem.title|escape}<img id="pic{$pictureItem.id}" src="resource2/ocstyle/images/misc/empty.png" longdesc="{$pictureItem.url}" /></a>
			{if $pictureItem.spoiler}
				({t}Spoiler{/t})
			{/if}
			{if $logItem.userid==$login.userid}
				&nbsp;
				[<a href="picture.php?action=edit&uuid={$pictureItem.uuid|escape}">{t}Edit{/t}</a>]
				[<a href="picture.php?action=delete&uuid={$pictureItem.uuid|escape}">{t}Delete{/t}</a>]
			{/if}
			<br />
		{/foreach}
	</div>
	</div>
</div>
