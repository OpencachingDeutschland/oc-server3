{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
{if $status==1}
	<img src="resource2/{$opt.template.style}/images/cachestatus/16x16-go.png" alt="{t}Available{/t}" />
{elseif $status==2}
	<img src="resource2/{$opt.template.style}/images/cachestatus/16x16-stop.png" alt="{t}Temporary disabled{/t}" />
{elseif $status==3}
	<img src="resource2/{$opt.template.style}/images/cachestatus/16x16-trash.png" alt="{t}Archived{/t}" />
{elseif $status==6}
	<img src="resource2/{$opt.template.style}/images/cachestatus/16x16-stop.png" alt="{t}Locked, visible{/t}" />
{elseif $status==7}
	<img src="resource2/{$opt.template.style}/images/cachestatus/16x16-trash.png" alt="{t}Locked, invisible{/t}" />	
{else}
	&nbsp;
{/if}