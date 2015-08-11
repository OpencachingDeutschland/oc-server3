{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/description/22x22-description.png" style="margin-right: 10px;" width="32" height="32" alt="{t}News{/t}" />
	{t}News{/t}
</div>

<table width="100%" class="table">
	{foreach name=news from=$news item=newsItem}
		<tr><td>{$newsItem.date_created|date_format:$opt.format.datetime} ({$newsItem.name}) {$newsItem.content}</td></tr>
	{/foreach}
</table>