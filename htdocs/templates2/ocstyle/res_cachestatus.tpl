{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
{if $status==1}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-go.png" alt="{t}Available{/t}" title="{t}Available{/t}" />
{elseif $status==2}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-stop.png" alt="{t}Temporarily not available{/t}" title="{t}Temporarily not available{/t}" />
{elseif $status==3}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-trash.png" alt="{t}Archived{/t}" title="{t}Archived{/t}" />
{elseif $status==4}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-wait.png" alt="{t}Hidden by approvers to check{/t}" title="{t}Hidden by approvers to check{/t}" />
{elseif $status==5}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-wait.png" alt="{t}Not yet published{/t}" title="{t}Not yet published{/t}" />
{elseif $status==6}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-locked.png" alt="{t}Locked, visible{/t}" title="{t}Locked, visible{/t}" />
{elseif $status==7}
    <img src="resource2/{$opt.template.style}/images/cachestatus/16x16-locked-invisible.png" alt="{t}Locked, invisible{/t}" title="{t}Locked, invisible{/t}" />
{else}
    &nbsp;
{/if}
