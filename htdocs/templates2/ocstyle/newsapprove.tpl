{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/description/22x22-description.png" style="margin-right: 10px;" width="22" height="22" alt="{t}News{/t}" title="{t}News{/t}" />
	{t}News{/t}
</div> 
 
<table class="table">
	{foreach name=newsEntry from=$newsentries item=newsItem}
		<tr>
			<td>
				{if !$newsItem.display}<span style="color:gray">{/if}
					{$newsItem.date_created|date_format:$opt.format.datetime} - {$newsItem.topic|escape}<br />
					{$newsItem.content}
				{if !$newsItem.display}</span>{/if}

				<br />
				{if !$newsItem.display}
					<a href="newsapprove.php?action=show&id={$newsItem.id|escape}">{t}Show{/t}</a>
				{else}
					<a href="newsapprove.php?action=hide&id={$newsItem.id|escape}">{t}Do not show{/t}</a>
				{/if}
				<a href="newsapprove.php?action=delete&id={$newsItem.id|escape}">{t}Delete{/t}</a>
			</td>
		</tr>
		<tr><td class="spacer"></td></tr>
	{/foreach}
</table>