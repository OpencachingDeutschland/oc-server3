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
		<p style="color: 5890a8"><b>{t}Attendees{/t}</b></p>
		<p style="color: 5890a8">({t}Total{/t}: {count array=$attendants})</p>
		<p>{foreach from=$attendants item=attendantsItem}
			{$attendantsItem.username|escape}<br />
		{/foreach}</p>
	</div>
</p>