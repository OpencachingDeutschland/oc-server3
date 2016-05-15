{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}{strip}
{* strip is needed by search.result.caches.row.tpl *}
{if $type==1}
    <img src="resource2/{$opt.template.style}/images/log/16x16-found.png" alt="{t}Found{/t}" title="{t}Found{/t}"  />
{elseif $type==2}
    <img src="resource2/{$opt.template.style}/images/log/16x16-dnf.png" alt="{t}Not found{/t}" title="{t}Not found{/t}" />
{elseif $type==3}
    <img src="resource2/{$opt.template.style}/images/log/16x16-note.png" alt="{t}Note{/t}" title="{t}Note{/t}" />
{elseif $type==7}
    <img src="resource2/{$opt.template.style}/images/log/16x16-attended.png" alt="{t}Attended{/t}" title="{t}Attended{/t}" />
{elseif $type==8}
    <img src="resource2/{$opt.template.style}/images/log/16x16-will_attend.png" alt="{t}Will attend{/t}" title="{t}Will attend{/t}" />
{elseif $type==9}
    <img src="resource2/{$opt.template.style}/images/log/16x16-archived.png" alt="{t}Archived{/t}" title="{t}Archived{/t}" />
{elseif $type==10}
    <img src="resource2/{$opt.template.style}/images/log/16x16-active.png" alt="{t}Available{/t}" title="{t}Available{/t}" />
{elseif $type==11}
    <img src="resource2/{$opt.template.style}/images/log/16x16-disabled.png" alt="{t}Temporarily not available{/t}" title="{t}Temporarily not available{/t}" />
{elseif $type==13}
    <img src="resource2/{$opt.template.style}/images/log/16x16-locked.png" alt="{t}Locked{/t}" title="{t}Locked{/t}" />
{elseif $type==14}
    <img src="resource2/{$opt.template.style}/images/log/16x16-locked-invisible.png" alt="{t}Locked, invisible{/t}" title="{t}Locked, invisible{/t}" />
{else}
    &nbsp;
{/if}
{/strip}
