{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{if $type==1}
	<img src="resource2/{$opt.template.style}/images/log/16x16-found.png" alt="{t}Found{/t}" />
{elseif $type==2}
	<img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" alt="{t}Not found{/t}" />
{elseif $type==3}
	<img src="resource2/{$opt.template.style}/images/log/16x16-note.png" alt="{t}Note{/t}" />
{elseif $type==7}
	<img src="resource2/{$opt.template.style}/images/log/16x16-attended.png" alt="{t}Attended{/t}" />
{elseif $type==8}
	<img src="resource2/{$opt.template.style}/images/log/16x16-will_attend.png" alt="{t}Will attend{/t}" />
{else}
	&nbsp;
{/if}
