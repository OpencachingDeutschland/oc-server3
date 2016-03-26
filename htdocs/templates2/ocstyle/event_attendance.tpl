{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<p>
	<div style="margin-top:4px;">
		<p style="color: 5890a8"><b>{$cachename|escape}</b></p>
		<p style="color: 5890a8">{t}at (time){/t} {$event_date|date_format:$opt.format.date}</font><br />
		{t}by{/t} {$owner|escape}</p>
	</div>
	<div style="margin-top:16px;">
		<p style="color: 5890a8"><b>{t}Will attend{/t}</b></p>
		<p style="color: 5890a8">({t}total:{/t} {count array=$willattend})</p>
		<p>{foreach from=$willattend item=attendantsItem}
			{$attendantsItem.username|escape}<br />
		{/foreach}</p>
	<div style="margin-top:16px;">
		<p style="color: 5890a8"><b>{t}Attended{/t}</b></p>
		<p style="color: 5890a8">({t}total:{/t} {count array=$attended})</p>
		<p>{foreach from=$attended item=attendantsItem}
			{$attendantsItem.username|escape}<br />
		{/foreach}</p>
	</div>
	</div>
</p>